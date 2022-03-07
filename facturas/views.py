from django.http import HttpResponse, HttpResponseRedirect
from django.urls import reverse_lazy
from django.shortcuts import render
from django.contrib.auth.mixins import LoginRequiredMixin
from django.views import generic
from django.contrib.messages.views import SuccessMessageMixin
from .models import Facturas, Factp, FormasPagos, frm_de_pagos
from generales.models import Terceros, Profile
from catalogos.models import Producto, Iva
from .forms import FacturaEncForm, FacturaDetForm, DetalleFacFormSet, FacturaPosEncForm, FacturaPosDetalleForm, DetalleMovimientosFormSet
from generales.views import SinPrivilegios
from fact_digital.utilidades import facturaDian, consultaRangos, fechaDian, HoraDian, AnulaFactura
from django.http import JsonResponse
from django.core.exceptions import ValidationError
from django.core.validators import validate_email
from pos.utilidades import enviar_mail, numero_to_letras
from datetime import date
from datetime import datetime, timedelta
from django.db.models import Sum
from PDFNetPython3 import *
import sys
from django.db.models import Sum, F

from static.base.LicenseKey import *


class FacturaList(LoginRequiredMixin, generic.ListView):
    login_url = 'generales:login'
    model=Facturas
    template_name="salidas/facturas_list.html"
    context_object_name="facturas"


class FacturaNew(LoginRequiredMixin, generic.CreateView):
    permission_required = 'Facturas.add_facturas'
    model = Facturas
    login_url = 'generales:login'
    template_name = 'facturas/factura_pos.html'
    form_class = FacturaPosEncForm
    success_url = reverse_lazy('generales:home')

    def get(self, request, *args, **kwargs):

        ctx = {'fecha_factura': datetime.today(), 'valor_factura':0, 'recibido':0, 'cambio':0, 'efectivo': '', 'tdebito': '', 'tcredito': '', 'transferencia': '', 'bonos': ''}

        self.object = None

        form = FacturaPosEncForm(initial=ctx)

        detalle_movimientos_formset = DetalleMovimientosFormSet()

        return self.render_to_response( 
            self.get_context_data(
                form=form,
                bar_code_read= '',
                frm_pagos = FormasPagos.objects.all().order_by('nombre'),
                detalle_movimientos=detalle_movimientos_formset            
            )
        )

    def post(self, request, *args, **kwargs):
        form =FacturaPosEncForm(request.POST)
        detalle_movimientos = DetalleMovimientosFormSet(request.POST)
        #print("SSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSS")
        #print(form.errors)
        #print(detalle_movimientos.errors)
        if form.is_valid() and detalle_movimientos.is_valid():
            return self.form_valid(form, detalle_movimientos)
        else:
            return self.form_invalid(form, detalle_movimientos)

    def form_valid(self, form, detalle_movimientos):
        self.object = form.save(commit=False)
        profile = Profile.objects.filter(user=self.request.user.id).get()
        fact = profile.factura + 1
        profile.factura = profile.factura + 1
        profile.save()
        self.object.factura = fact
        self.object.observacion = "Factura POS"
        self.object.usuario = self.request.user
        total, iva = 0, 0
        for detalle in detalle_movimientos:
            if not detalle.cleaned_data:
                continue
            if not detalle.cleaned_data["cantidad"]:
                continue
            tot_unidad = detalle.cleaned_data["valor_unidad"] * detalle.cleaned_data["cantidad"]
            total += tot_unidad
            producto=Producto.objects.filter(codigo_de_barra=detalle.cleaned_data["codigo_de_barra"], usuario=self.request.user.id).get()
            iva += round((producto.tarifa_iva.tarifa_iva * tot_unidad / 100),0)
            producto.existencia = producto.existencia - detalle.cleaned_data["cantidad"]
            producto.save()
        self.object.valor_factura = total
        self.object.valor_iva = iva
        self.object = form.save()
        detalle_movimientos.instance = self.object
        detalle_movimientos.save()
        
#        return HttpResponseRedirect(reverse_lazy("facturas:resul_pos"))
        
        return HttpResponseRedirect(reverse_lazy('facturas:resul_pos', kwargs={'factura': fact, \
                                    'total': self.object.valor_factura, \
                                    'recibido': self.object.recibido, \
                                    'cambio': self.object.cambio, \
                                    'efectivo': self.object.efectivo, \
                                    'tdebito': self.object.tdebito, \
                                    'tcredito': self.object.tcredito, \
                                    'transferencia': self.object.transferencia, \
                                    'bonos': self.object.bonos }))

    def form_invalid(self, form, detalle_movimientos):
        self.object=form
        return self.render_to_response(
            self.get_context_data(
                form=form,
                detalle_movimientos=detalle_movimientos
            )
        )
        
        
    
def resul_pos(request, factura, total, recibido, cambio, efectivo, tdebito, tcredito, transferencia, bonos):
    factp = Factp.objects.filter(factura__factura=factura, factura__usuario=request.user)
    tarifas_iva = Iva.objects.all()
    iva={}
    for j,item in enumerate(tarifas_iva):
        iva['tarifa'+str(j)]=item.tarifa_iva
        base, valor = 0,0
        for i, line in enumerate(factp):
            if not line.porc_iva:
                continue
            if line.porc_iva == item.tarifa_iva:
                base += line.valor_unidad
                valor += line.valor_iva
        iva['base'+str(j)] = int(base)
        iva['valor'+str(j)] = int(valor)     
    ctx={
        'empresa':request.user.profile.empresa,
        'nit': request.user.profile.nit,
        'direccion': request.user.profile.direccion,
        'telefono': request.user.profile.telefono,
        'dian': request.user.profile.r_dian,
        'logo': request.user.profile.logo,
        'detalle': factp,
        'factura': Facturas.objects.filter(factura=factura, usuario=request.user).last()
    }

    return render(request, "facturas/resul_pos.html", context={'tarifas_iva': tarifas_iva, 'ctx': ctx, 'factura': factura, 'total': total, 'recibido': recibido, 'cambio': cambio, 'iva': iva, 'efectivo': efectivo, 'tdebito': tdebito, 'tcredito': tcredito, 'transferencia': transferencia, 'bonos': bonos})



class CierreCajaView(LoginRequiredMixin, generic.ListView):
    
    model = Facturas
    template_name = "facturas/info_cierrecaja.html"
    context_object_name = "obj"
    login_url='generales:login'

    def get(self, request, *args, **kwargs):
        ano = int(request.GET.get('periodo')[0:4])
        mes = int(request.GET.get('periodo')[5:7])
        movimientos = Factp.objects.filter(facturas__fecha_factura__month=mes, facturas__fecha_factura__year=ano, facturas__usuario=request.user)
        context = {}
        context['mes'] = mes
        context['ano'] = ano
        context['empresa'] = request.user.profile.empresa
        context['movimientos'] = movimientos
        self.object_list = context

        return self.render_to_response(
            self.get_context_data(
                context = context
            )
        )



def imprimir(request, factura, total, recibido, cambio):
    factp = Factp.objects.filter(factura__factura=factura, factura__usuario=request.user)
    tarifas_iva = Iva.objects.all()
    iva={}
    for j,item in enumerate(tarifas_iva):
        iva['tarifa'+str(j)]=item.tarifa_iva
        base, valor = 0,0
        for i, line in enumerate(factp):
            if not line.porc_iva:
                continue
            if line.porc_iva == item.tarifa_iva:
                base += line.valor_unidad
                valor += line.valor_iva
        iva['base'+str(j)] = int(base)
        iva['valor'+str(j)] = int(valor)     
    ctx={
        'empresa':request.user.profile.empresa,
        'nit': request.user.profile.nit,
        'direccion': request.user.profile.direccion,
        'telefono': request.user.profile.telefono,
        'dian': request.user.profile.r_dian,
        'logo': request.user.profile.logo,
        'factura': Facturas.objects.filter(factura=factura, usuario=request.user).last()
    }
    return render(request, "facturas/imprimir.html", context={'tarifas_iva': tarifas_iva, 'ctx': ctx, 'factura': factura, 'total': total, 'recibido': recibido, 'cambio': cambio, 'iva': iva})


def imprimirTiquete(request, factura):
    import io
    import os
    from django.conf import settings
    from reportlab.platypus import SimpleDocTemplate, Paragraph, TableStyle, BaseDocTemplate
    from reportlab.lib.enums import TA_JUSTIFY, TA_LEFT, TA_CENTER, TA_RIGHT
    from reportlab.lib.styles import ParagraphStyle, getSampleStyleSheet
    from reportlab.lib import colors  
    from reportlab.lib.pagesizes import letter, landscape, portrait
    from reportlab.platypus import Table
    from reportlab.lib.units import inch, mm
    from reportlab.platypus import Image, PageBreak, Paragraph, Spacer
    from django.core.mail import EmailMessage
    from io import StringIO
    from reportlab.pdfgen import canvas
    from reportlab_qrcode import QRCodeImage
    from reportlab.graphics.barcode import code128

    response = HttpResponse(content_type='application/pdf')  
    buffer = io.BytesIO()

     
    ordenes = []
    logo_path = request.user.profile.foto.url
    logo = os.path.join(settings.BASE_DIR, logo_path)
    #texto_path = "static/base/images/texto-inrai.jpg"
    #texto = os.path.join(settings.BASE_DIR, texto_path)
    #image = Image(logo, 3 * inch, .8 * inch)
    #image.hAlign = "LEFT"
    image1 = Image(logo, 2.5 * inch, .8 * inch)
    image1.hAlign = "LEFT"
    styles = getSampleStyleSheet()
    styles.add(ParagraphStyle(name='Normal_CENTER', alignment=TA_CENTER))

    factp = (Factp.objects.filter(factura__factura=factura, factura__usuario=request.user))
    total_doc = 0
    for i,item in enumerate(factp):
        producto = item
        fecha = item.factura.fecha_factura  

    filename = "invoice_{}.pdf".format(factura)
    titulo = "FACTURA DE VENTA # {}".format(factura)
    qr = QRCodeImage(str(factura), size=30 * mm)
    qr.hAlign = "RIGHT"
    


    t=Table(
        data=[
            [image1,''],
            [request.user.profile.empresa,''],
            [request.user.profile.nit,''],
            [request.user.profile.direccion,''],
            [request.user.profile.telefono,''],
            ['',''],
            [request.user.profile.r_dian],
            ['Fecha: ', fecha.strftime('%d/%m/%Y')],
            ['DOCUMENTO No. ', factura],
            ['Condicion de Pago: ', 'CONTADO'],
        ],
        colWidths=[100,30],
        style=[
                ("FONT", (0,0), (9,1), "Helvetica", 2, 4),
                ('VALIGN',(1,0), (4,1),'MIDDLE'),
                ('ALIGN',(1,0),(4,1),'CENTRE'),
            ]
        )

    t.hAlign = "LEFT"
    ordenes.append(t)
    ordenes.append(Spacer(1, 5))
    headings0 = ('CANTIDAD', 'DESCRIPCION', 'VALOR')
    recibos2=[]
    t_cantidad = 0
    t_total = 0
    for lin, reg in enumerate(factp):
        t_cantidad = t_cantidad +  reg.cantidad
        t_total = t_total + reg.cantidad * reg.valor_unidad

        recibos2.append([
            reg.cantidad,
            reg.producto,
            '${:,}'.format(reg.valor_unidad)
            ])

    recibos2.append([
    'TOTAL',
    '',
    '${:,}'.format(t_total)
    ])


    t0 = Table([headings0] + recibos2, colWidths=[35,105,50])
    t0.setStyle(TableStyle(
    [  
        ('GRID', (0, 0), (2, -1), 1, colors.gray),  
        ('LINEBELOW', (0, 0), (-1, 0), 1, colors.gray),
        ('TEXTCOLOR', (0, 0), (-1, 0), colors.white),
        ('FONT', (0, 0), (2, -1), "Helvetica", 6,6),
        ('FONTSIZE', (0, 0), (2, -1), 6),
        ('BACKGROUND', (0, 0), (2,0), colors.gray)  
    ]  
    ))
    t0.hAlign = "LEFT"

    ordenes.append(t0)

    ordenes.append(Spacer(1, 5))
    icon_path = "/static/base/images/favicon.png"
    icon = os.path.join(settings.BASE_DIR, icon_path)
    doc = SimpleDocTemplate(buffer,
                ###pagesize=portrait(3),
                rightMargin=0,
                leftMargin=0,
                topMargin=2,  
                bottomMargin=8,
                author="POS",
                title=titulo,
                icon=icon,
                )
    
    
    
    doc.build(ordenes)
    response = HttpResponse(content_type='application/pdf')
    response['Content-Disposition'] = 'attachment; filename=%s' % filename
    response.write(buffer.getvalue())
    buffer.close()

    
    
    imprimir_xxx("facturas/invoice_58.pdf")
    return response


    
def imprimir_xxx(filename):

    PDFNet.Initialize(LicenseKey)
    
    # Relative path to the folder containing the test files.
    input_path = "../../TestFiles/"
    
    doc = PDFDoc(filename)
    doc.InitSecurityHandler()
    
    # Set our PrinterMode options
    printerMode = PrinterMode()
    printerMode.SetCollation(True)
    printerMode.SetCopyCount(1)
    printerMode.SetDPI(100); # regardless of ordering, an explicit DPI setting overrides the OutputQuality setting
    printerMode.SetDuplexing(PrinterMode.e_Duplex_Auto)
    
    # If the XPS print path is being used, then the printer spooler file will
    # ignore the grayscale option and be in full color
    printerMode.SetOutputColor(PrinterMode.e_OutputColor_Grayscale)
    printerMode.SetOutputQuality(PrinterMode.e_OutputQuality_Medium)
    # printerMode.SetNUp(2,1)
    # printerMode.SetScaleType(PrinterMode.e_ScaleType_FitToOutPage)
    
    # Print the PDF document to the default printer, using "tiger.pdf" as the document
    # name, send the file to the printer not to an output file, print all pages, set the printerMode
    # and don't provide a cancel flag.
    Print.StartPrintJob(doc, "", doc.GetFileName(), "", None, printerMode, None)
    PDFNet.Terminate()

    return
    




#SinPrivilegios, 
class FacturaEdit(generic.UpdateView):
    pass



def MenuView(request, *args, **kwargs):
    template_name="facturas/menu.html"
    hoy = date.today()
    #form = SedesForm()
    anoR = hoy.year
    ventas=[]
    ene=0
    feb=0
    mar=0
    abr=0
    may=0
    jun=0
    jul=0
    ago=0
    sep=0
    oct=0
    nov=0
    dic=0
        
    for mes in range(1,13):
        lista=[]
        r = Facturas.objects.filter(fecha_factura__month=mes, fecha_factura__year=anoR).aggregate(
            Sum('valor_factura'),
            Sum('valor_iva'),
            Sum('reteica'),
            Sum('reteiva'),
            Sum('retfuente'),
            Sum('saldo'))
        
        anulados = Facturas.objects.filter(fecha_factura__month=mes, fecha_factura__year=anoR, anulado=True).aggregate(
            Sum('valor_factura'),
            Sum('valor_iva'),
            Sum('reteica'),
            Sum('reteiva'),
            Sum('retfuente'),
            Sum('saldo'))

       # nd = NotasDebito.objects.filter(fecha__month=mes, fecha__year=anoR, sede=1).aggregate(
       #     Sum('factura__factura__valor_factura'),
       #     Sum('factura__factura__valor_iva'),
       #     Sum('factura__factura__reteica'),
      #     Sum('factura__factura__reteiva'),
       #     Sum('factura__factura__retfuente'),
       #     Sum('factura__factura__saldo'))

       # nc = FacturasAnuladas.objects.filter(fecha__month=mes, fecha__year=anoR, sede=1).aggregate(
       #     Sum('factura__factura__valor_factura'),
       #     Sum('factura__factura__valor_iva'),
       #     Sum('factura__factura__reteica'),
       #     Sum('factura__factura__reteiva'),
       #     Sum('factura__factura__retfuente'),
        #    Sum('factura__factura__saldo'))

        if r["valor_factura__sum"] is not None:
            if mes == 1:
                ene = ene + r["valor_factura__sum"]
            elif mes == 2:
                feb = feb + r["valor_factura__sum"]
            elif mes == 3:
                mar = mar + r["valor_factura__sum"]
            elif mes == 4:
                abr = abr + r["valor_factura__sum"]
            elif mes == 5:
                may = may + r["valor_factura__sum"]
            elif mes == 6:
                jun = jun + r["valor_factura__sum"]
            elif mes == 7:
                jul = jul + r["valor_factura__sum"]
            elif mes == 8:
                ago = ago + r["valor_factura__sum"]
            elif mes == 9:
                sep = sep + r["valor_factura__sum"]
            elif mes == 10:
                oct = oct + r["valor_factura__sum"]
            elif mes == 11:
                nov = nov + r["valor_factura__sum"]
            else:
                dic = dic + r["valor_factura__sum"]

        lista.append(r)
        lista.append(anulados)
        #lista.append(nd)
        #lista.append(nc)
        context={}
        context['tot_'+str(mes)] = lista

    ventas.append(int(ene))
    ventas.append(int(feb))
    ventas.append(int(mar))
    ventas.append(int(abr))
    ventas.append(int(may))
    ventas.append(int(jun))
    ventas.append(int(jul))
    ventas.append(int(ago))
    ventas.append(int(sep))
    ventas.append(int(oct))
    ventas.append(int(nov))
    ventas.append(int(dic))
    context['ventas'] = ventas
    context['hoy'] = hoy

    resul = Facturas.objects.filter(saldo__gte=0,anulado=False)
    vencimientos=[]
    sdo30=0
    sdo60=0
    sdo90=0
    sdootros=0
    total=0
    for i, item in enumerate(resul):
        total += int(item.saldo)
        if (hoy-item.fecha_factura).days<=31:
            sdo30 += int(item.saldo)
        elif (hoy-item.fecha_factura).days<=61:
            sdo60 += int(item.saldo)
        elif (hoy-item.fecha_factura).days<=91:
            sdo90 += int(item.saldo)
        else:
            sdootros += int(item.saldo)
    
    vencimientos.append(sdo30)
    vencimientos.append(sdo60)
    vencimientos.append(sdo90)
    vencimientos.append(sdootros)
    context['vencimientos'] = vencimientos
    context['total'] = total
    #p=Emisoras.objects.filter(categoria__lte=3, url_streaming=None).aggregate(sin_url = Count('id'))
    #context['sin_url'] = p['sin_url']
    #r=Emisoras.objects.filter(categoria__lte=3).aggregate(total=Count('id'))
    #context['total_emisoras'] = r['total']
    #context['netos'] = r['total']-p['sin_url']

    return render(request, template_name, context)



def get_ajaxBarcode(request, *args, **kwargs): 
    bar_code = request.GET.get('bar_code', None)
    if not bar_code:
        return JsonResponse(data={'nombre': '', 'errors': 'No encuentro producto.'})
    else:
        bar_code = Producto.objects.filter(codigo_de_barra=bar_code, usuario=request.user).last()
        if bar_code:
            return JsonResponse(data={"nombre": bar_code.nombre, "valor_unidad": bar_code.precio_de_venta, "porc_iva": bar_code.tarifa_iva.tarifa_iva, "codigo_de_barra": bar_code.codigo_de_barra}, safe=False)
        else: 
            return JsonResponse(data={'nombre': '', 'errors': 'No encuentro producto.'})
