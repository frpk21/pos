from django.http import HttpResponse, HttpResponseRedirect
from django.urls import reverse_lazy
from django.shortcuts import render
from django.contrib.auth.mixins import LoginRequiredMixin
from django.views import generic
from django.contrib.messages.views import SuccessMessageMixin
from .models import Facturas, Factp, FormasPagos, Cierres, Cierres1, GrabadosCierres1, FormasPagosCierres1, Vales, PagosCartera
from generales.models import Terceros, Profile
from catalogos.models import Producto, Iva
from .forms import FacturaEncForm, FacturaDetForm, DetalleFacFormSet, FacturaPosEncForm, FacturaPosDetalleForm, DetalleMovimientosFormSet, ValesForm
from generales.views import SinPrivilegios
from fact_digital.utilidades import facturaDian, consultaRangos, fechaDian, HoraDian, AnulaFactura
from django.http import JsonResponse
from django.core.exceptions import ValidationError
from django.core.validators import validate_email
from pos.utilidades import enviar_mail, numero_to_letras
from datetime import date
from datetime import datetime, timedelta
from django.utils import timezone
from django.db.models import Sum
from PDFNetPython3 import *
import sys
from django.db.models import Sum, F
from decimal import Decimal
from static.base.LicenseKey import *
from django.db.models import Max, Min


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
        
        if get_ajax_valida_cierres(request) == True:
            ctx = {'fecha_factura': datetime.today(), 'valor_factura':0, 'valor_iva':0, 'recibido':0, 'cambio':0, 'efectivo': '', 'tdebito': '', 'tcredito': '', 'transferencia': '', 'bonos': '', 'credito': '', 'descuento': '', 'tercero': request.user.profile.tercero_mostrador.id}

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
        else:
            return HttpResponseRedirect(reverse_lazy("facturas:menu"))

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
        descto = self.object.descuento
        self.object.observacion = "Factura POS"
        self.object.usuario = self.request.user
        if self.object.credito == 0:
            self.object.tercero = profile.tercero_mostrador
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
        self.object.saldo_credito = self.object.credito
        self.object = form.save()
        detalle_movimientos.instance = self.object
        detalle_movimientos.save()
        
#        return HttpResponseRedirect(reverse_lazy("facturas:resul_pos"))
        
        return HttpResponseRedirect(reverse_lazy('facturas:resul_pos', kwargs={'factura': fact, \
                                    'total': self.object.valor_factura, \
                                    'iva_pagado': iva, \
                                    'neto': self.object.valor_factura - descto + iva, \
                                    'recibido': self.object.recibido, \
                                    'cambio': self.object.cambio, \
                                    'efectivo': self.object.efectivo, \
                                    'tdebito': self.object.tdebito, \
                                    'tcredito': self.object.tcredito, \
                                    'transferencia': self.object.transferencia, \
                                    'bonos': self.object.bonos, \
                                    'credito': self.object.credito, \
                                    'descuento': self.object.descuento}))

    def form_invalid(self, form, detalle_movimientos):
        self.object=form
        return self.render_to_response(
            self.get_context_data(
                form=form,
                detalle_movimientos=detalle_movimientos
            )
        )
        
        
    
def resul_pos(request, factura, total, iva_pagado, neto, recibido, cambio, efectivo, tdebito, tcredito, transferencia, bonos, credito, descuento):
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

    return render(request, "facturas/resul_pos.html", context={'tarifas_iva': tarifas_iva, 'ctx': ctx, 'factura': factura, 'total': (int(total)+int(iva_pagado)), 'recibido': recibido, 'cambio': cambio, 'iva': iva, 'iva_total': iva_pagado, 'efectivo': efectivo, 'tdebito': tdebito, 'tcredito': tcredito, 'transferencia': transferencia, 'bonos': bonos, 'credito': credito , 'descuento': descuento, 'neto': neto })



class CierreCajaView(LoginRequiredMixin, generic.ListView):
    model = Cierres
    template_name = "facturas/info_cierrecaja.html"
    context_object_name = "obj"
    login_url='generales:login'

    def get(self, request, *args, **kwargs):
        ventas = Cierres.objects.filter(usuario=self.request.user).order_by('-id')[:30]
        context = {}
        context['empresa'] = request.user.profile.empresa
        context['ventas'] = ventas
        self.object_list = context

        return self.render_to_response(
            self.get_context_data(
                context = context,
                hoy = datetime.now(tz=timezone.utc)
            )
        )
    
def CierreDoing(request):
    base_caja = request.GET.get('base_caja', 0)
    base_caja = int(base_caja)
    ventas = Factp.objects.filter(factura__cerrado=False, factura__usuario=request.user)
    if ventas:
        minimo = ventas.aggregate(Min('factura__factura'))
        maximo = ventas.aggregate(Max('factura__factura'))
        total=0
        profile = Profile.objects.filter(user=request.user.id).get()
        cierre = profile.cierre + 1
        profile.cierre = profile.cierre + 1
        profile.save()
        for i,item in enumerate(ventas):
            total+= (item.valor_unidad * item.cantidad) + item.valor_iva
        
        Cierres.objects.get_or_create(
            fecha = datetime.now(tz=timezone.utc),
            cierre_no = cierre,
            valor_total_registrado = total,
            base_caja = base_caja,
            usuario = request.user,
            pos_no = 1,
            factura_desde = minimo["factura__factura__min"],
            factura_hasta = maximo["factura__factura__max"]
        )
        Facturas.objects.filter(usuario=request.user.id, cerrado=False).update(cerrado=True)
        Vales.objects.filter(usuario=request.user.id, cerrado=False).update(cerrado=True)
        return JsonResponse(data={'cierre': cierre, 'errors': ''})
    else:
        return JsonResponse(data={'cierre': '', 'errors': 'No hay movieminetos para cerrar.'})




def imprimirCierre(request, cierre):
    cierre = int(cierre)
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
    recaudos = PagosCartera.objects.filter(modificado=datetime.today(), factura__usuario=request.user)
    vales = Vales.objects.filter(modificado=datetime.today(), usuario=request.user)
    total_recaudo = 0
    for i, item in enumerate(recaudos):
        total_recaudo += item.valor_pago
    total_vales = 0
    for i, item in enumerate(recaudos):
        total_vales += item.valor
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
    cierre = (Cierres.objects.get(cierre_no=cierre, usuario=request.user))
    cierre1 = (Cierres1.objects.get(cierre = cierre.id))
    grabados = (GrabadosCierres1.objects.filter(cierre = cierre.id))
    frm_pagos = (FormasPagosCierres1.objects.filter(cierre = cierre.id))
    filename = "invoice_{}.pdf".format(cierre)
    titulo = "CIERRE # {}".format(cierre)
    qr = QRCodeImage(str(cierre), size=30 * mm)
    qr.hAlign = "RIGHT"
    tot = Paragraph('C I E R R E  D E  C A J A')
    tot.hAlign = "TA_CENTER"
    ordenes.append(tot)
    t=Table(
        data=[
            ['',''],
            [request.user.profile.empresa,''],
            [request.user.profile.nit,''],
            [request.user.profile.direccion,''],
            [request.user.profile.telefono,''],
            [request.user.profile.r_dian,''],
            ['Fecha: ', cierre.fecha.strftime('%d/%m/%Y, %H:%M:%S')],
            ['Cierre No. ', cierre.cierre_no],
            ['Base Caja: ', '${:,}'.format(cierre.base_caja)],
            ['Total Caja','${:,}'.format(cierre.base_caja+cierre.valor_total_registrado+total_recaudo-total_vales)],
        ],
        colWidths=[100,30],
        style=[
                ("FONT", (0,0), (9,1), "Helvetica", 2, 4),
                ('VALIGN',(1,0), (4,1),'MIDDLE'),
                ('ALIGN',(1,0),(4,1),'CENTRE'),
                ('FONTSIZE', (0, 0), (9, -1), 8),
            ]
        )
    t.hAlign = "LEFT"
    ordenes.append(t)
    ordenes.append(Spacer(1, 5))
    headings0 = ('TOTAL VENTA', 'FACT-DESDE', 'FACT-HASTA')
    recibos2=[]
    recibos2.append([
        '${:,}'.format(cierre.valor_total_registrado),
        cierre.factura_desde,
        cierre.factura_hasta
        #'${:,}'.format(reg.valor_unidad)
        ])
    t0 = Table([headings0] + recibos2, colWidths=[70,45,45])
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

# CIERRE1
    ordenes.append(Spacer(1, 5))
    t=Table(
        data=[
            ['',''],
            ['',''],
            ['DESCUENTOS', '${:,}'.format(cierre1.ventas_descuentos)],
            ['VENTAS EXCENTAS', '${:,}'.format(cierre1.ventas_cubcat_excentas)],
            ['VENTAS EXCLUIDAS', '${:,}'.format(cierre1.ventas_cubcat_excluidas)],
            ['VENTAS GRABADAS','${:,}'.format(cierre1.ventas_cubcat_grabadas)],
            ['RECAUDOS DE CARTERA','${:,}'.format(total_recaudo)],
            ['VALES', '-${:,}'.format(total_vales)],
            ['', ''],
            ['', ''],
        ],
        colWidths=[120,30],
        style=[
                ("FONT", (0,0), (9,1), "Helvetica", 3),
                ('VALIGN',(1,0), (4,1),'MIDDLE'),
                ('ALIGN',(1,0),(4,1),'CENTRE'),
                ('FONTSIZE', (0, 0), (9, -1), 6),
            ]
        )

    t.hAlign = "LEFT"
    ordenes.append(t)
# END CIERRE1
# GRABADOS CIERRES1
    tot = Paragraph('INFORMACION TRIBUTARIA')
    tot.hAlign = "TA_RIGHT"
    ordenes.append(tot)
    headings0 = ('TARIFA', 'BASE IVA', 'VALOR IVA')
    recibos2=[]
    for i, item in enumerate(grabados):
        recibos2.append([
            '{:,}'.format(item.tarifa_iva),
            '${:,}'.format(item.base_iva),
            '${:,}'.format(item.valor_iva)
            ])
    t0 = Table([headings0] + recibos2, colWidths=[40,60,60])
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
# END GRABADOS CIERRES1
# FORMAS DE PAGO
    ordenes.append(Spacer(1, 5))
    tot = Paragraph('FORMAS DE PAGO')
    tot.hAlign = "TA_RIGHT"
    ordenes.append(tot)
    headings0 = ('FORMA DE PAGO', 'VALOR')
    recibos2=[]
    for i, item in enumerate(frm_pagos):
        recibos2.append([
            item.forma_pago,
            '${:,}'.format(item.valor_pago)
            ])
    t0 = Table([headings0] + recibos2, colWidths=[100,60])
    t0.setStyle(TableStyle(
    [  
        ('GRID', (0, 0), (1, -1), 1, colors.gray),  
        ('LINEBELOW', (0, 0), (-1, 0), 1, colors.gray),
        ('TEXTCOLOR', (0, 0), (-1, 0), colors.white),
        ('FONT', (0, 0), (1, -1), "Helvetica", 6,6),
        ('FONTSIZE', (0, 0), (1, -1), 6),
        ('BACKGROUND', (0, 0), (1,0), colors.gray)  
    ]  
    ))
    t0.hAlign = "LEFT"

    ordenes.append(t0)
# END FORMAS DE PAGO
# RECAUDOS
    ordenes.append(Spacer(1, 5))
    tot = Paragraph('RECAUDOS DE CARTERA')
    tot.hAlign = "TA_RIGHT"
    ordenes.append(tot)
    headings0 = ('PAGO No.', 'CLIENTE', 'VALOR')
    recibos2=[]
    for i, item in enumerate(recaudos):
        recibos2.append([
            item.pago_no,
            item.factura.tercero.rzn_social,
            '${:,}'.format(item.valor_pago)
            ])
    t0 = Table([headings0] + recibos2, colWidths=[32,75,55])
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
# END RECAUDOS
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
    response.write(buffer.getvalue())
    pdf = buffer.getvalue()
    buffer.close()

    return response


    


#SinPrivilegios, 
class FacturaEdit(generic.UpdateView):
    pass



def MenuView(request, *args, **kwargs):
    template_name="facturas/menu.html"
    hoy = datetime.now(tz=timezone.utc)
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



def MenuIndicadoresView(request, *args, **kwargs):
    template_name="facturas/menu_ind.html"
    hoy = date.today()
    ventas=[]
    h_0=0
    h_2=0
    h_4=0
    h_6=0
    h_8=0
    h_10=0
    h_12=0
    h_14=0
    h_16=0
    h_18=0
    h_20=0
    h_22=0
    r = Facturas.objects.filter(fecha_factura__date=hoy, anulado=False)
    #.aggregate(
    #    Sum('valor_factura'),
    #    Sum('valor_iva'),
    #    Sum('reteica'),
    #    Sum('reteiva'),
    #    Sum('retfuente'),
    #    Sum('saldo'))

    #
    # for hora in range(0,24):
    tot_dia = 0
    for i, item in enumerate(r):
        lista=[]
        if int(item.fecha_factura.hour) >=2 and int(item.fecha_factura.hour) <= 3:
            h_2 = h_2 + item.valor_factura
        elif int(item.fecha_factura.hour) >=4 and int(item.fecha_factura.hour) <= 5:
            h_4 = h_4 + item.valor_factura
        elif int(item.fecha_factura.hour) >=6 and int(item.fecha_factura.hour) <= 7:
            h_6 = h_6 + item.valor_factura
        elif int(item.fecha_factura.hour) >=8 and int(item.fecha_factura.hour) <= 9:
            h_8 = h_8 + item.valor_factura
        elif int(item.fecha_factura.hour) >=10 and int(item.fecha_factura.hour) <= 11:
            h_10 = h_10 + item.valor_factura
        elif int(item.fecha_factura.hour) >=11 and int(item.fecha_factura.hour) <= 13:
            h_11 = h_11 + item.valor_factura
        elif int(item.fecha_factura.hour) >=12 and int(item.fecha_factura.hour) <= 14:
            h_12 = h_12 + item.valor_factura
        elif int(item.fecha_factura.hour) >=14 and int(item.fecha_factura.hour) <= 16:
            h_14 = h_14 + item.valor_factura
        elif int(item.fecha_factura.hour) >=16 and int(item.fecha_factura.hour) <= 18:
            h_16 = h_16 + item.valor_factura
        elif int(item.fecha_factura.hour) >=18 and int(item.fecha_factura.hour) <= 20:
            h_18 = h_18 + item.valor_factura
        elif int(item.fecha_factura.hour) >=20 and int(item.fecha_factura.hour) <= 21:
            h_20 = h_20 + item.valor_factura
        elif int(item.fecha_factura.hour) >=22 and int(item.fecha_factura.hour) <= 24:
            h_22 = h_22 + item.valor_factura
        else:
            h_0 = h_0 + item.valor_factura
        lista.append(r)
        tot_dia += item.valor_factura
    context={}
    context['total_dia'] = tot_dia
    ventas.append(int(h_0))
    ventas.append(int(h_2))
    ventas.append(int(h_4))
    ventas.append(int(h_6))
    ventas.append(int(h_8))
    ventas.append(int(h_10))
    ventas.append(int(h_12))
    ventas.append(int(h_14))
    ventas.append(int(h_16))
    ventas.append(int(h_18))
    ventas.append(int(h_20))
    ventas.append(int(h_22))
    ventas.append(int(h_22))
    context['ventas'] = ventas
    context['hoy'] = hoy

    resul = Facturas.objects.filter(saldo_credito__gt=0,anulado=False)
    vencimientos=[]
    sdo30=0
    sdo60=0
    sdo90=0
    sdootros=0
    total=0
    for i, item in enumerate(resul):
        total += int(item.saldo_credito)
        if (hoy-item.fecha_factura.date()).days<=31:
            sdo30 += int(item.saldo_credito)
        elif (hoy-item.fecha_factura.date()).days<=61:
            sdo60 += int(item.saldo_credito)
        elif (hoy-item.fecha_factura.date()).days<=91:
            sdo90 += int(item.saldo_credito)
        else:
            sdootros += int(item.saldo_credito)
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
            return JsonResponse(data={
                "nombre": bar_code.nombre, 
                "valor_unidad": bar_code.precio_de_venta, 
                "porc_iva": bar_code.tarifa_iva.tarifa_iva,
                "valor_total": round(bar_code.precio_de_venta + Decimal(bar_code.precio_de_venta * Decimal(bar_code.tarifa_iva.tarifa_iva) / 100),0),  
                "codigo_de_barra": bar_code.codigo_de_barra,
                "prod": bar_code.id
                }, safe=False)
        else: 
            return JsonResponse(data={'nombre': '', 'errors': 'No encuentro producto.'})



def get_ajax_valida_cierres(request): 
    cerrados = Facturas.objects.filter(usuario=request.user.id, cerrado=False, fecha_factura__lt=date.today() )
    if cerrados:
        return False #JsonResponse(data={'errors': 'Hay cierres de caja pendientes por realizar, No puede facturar hasta realizar estos cierres.'})
    else:
        return True #JsonResponse(data={'errors': ''})



def get_ajax_pago_cartera(request, *args, **kwargs): 
    id = request.GET.get('id', None)
    valor_pago = int(request.GET.get('pago', None))
    if not id:
        return JsonResponse(data={'id': '', 'errors': 'No encuentro factura.'})
    else:
        resul = Facturas.objects.filter(id=id).last()
        if resul:
            if valor_pago > resul.saldo_credito:
                return JsonResponse(data={'id': '', 'errors': 'El valor del pago No puede ser mayor que el saldo pendiente por pagar.'})
            else:
                s_anterior = resul.saldo_credito
                resul.saldo_credito = resul.saldo_credito - valor_pago
                resul.save()
                profile = Profile.objects.filter(user=request.user.id).get()
                pago_no = profile.pago_no + 1
                profile.pago_no = profile.pago_no + 1
                profile.save()
                PagosCartera.objects.get_or_create(
                    fecha = datetime.now(tz=timezone.utc),
                    pago_no = pago_no,
                    factura = resul,
                    valor_pago = valor_pago,
                    saldo_anterior = s_anterior,
                    saldo_nuevo = resul.saldo_credito
                )
                return JsonResponse(data={
                    "saldo": resul.saldo_credito,
                    "errors": ""
                    }, safe=False)
        else: 
            return JsonResponse(data={'id': '', 'errors': 'No encuentro cliente.'})


def get_ajax_valida_nit(request, *args, **kwargs): 
    nitr = request.GET.get('nitr', None)
    if not nitr:
        return JsonResponse(data={'id': '', 'nombre': '', 'errors': 'NIT No Existe.'})
    else:
        resul = Terceros.objects.filter(nit=nitr, user=request.user).last()
        if resul:
            return JsonResponse(data={'id': resul.id, 'nombre': resul.rzn_social, 'errors': ''})
        else: 
            return JsonResponse(data={'id': '', 'nombre': '', 'errors': 'No encuentro cliente.'})



class ValesList(LoginRequiredMixin, generic.ListView):
    login_url = 'generales:login'
    model=Vales
    template_name="facturas/vales_list.html"
    context_object_name="vales"
    
    
class ValesNew(LoginRequiredMixin, generic.CreateView):
    permission_required = 'Vales.add_vales'
    model = Vales
    login_url = 'generales:login'
    template_name = 'facturas/vales_form.html'
    form_class = ValesForm
    success_url = reverse_lazy('generales:home')

    def get(self, request, *args, **kwargs):
        ctx = {'fecha': datetime.today(), 'valor':0, 'concepto': '', 'beneficiaro': ''}
        self.object = None
        form = ValesForm(initial=ctx)

        return self.render_to_response( 
            self.get_context_data(
                form=form           
            )
        )

    def post(self, request, *args, **kwargs):
        form =ValesForm(request.POST)
        #print("SSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSS")
        #print(form.errors)
        #print(detalle_movimientos.errors)
        if form.is_valid():
            return self.form_valid(form)
        else:
            return self.form_invalid(form)

    def form_valid(self, form):
        self.object = form.save(commit=False)
        profile = Profile.objects.filter(user=self.request.user.id).get()
        vale_no = profile.vales + 1
        profile.vales = profile.vales + 1
        profile.save()
        self.object.vale_no = vale_no
        self.object.usuario = self.request.user
        self.object.fecha = datetime.now(tz=timezone.utc)
        self.object = form.save()
  
        return HttpResponseRedirect(reverse_lazy('facturas:resul_vales', kwargs={'vale_no': vale_no, \
                                    'fecha': self.object.fecha, \
                                    'beneficiario': self.object.beneficiario, \
                                    'concepto': self.object.concepto, \
                                    'valor': self.object.valor}))

    def form_invalid(self, form):
        self.object=form
        return self.render_to_response(
            self.get_context_data(
                form=form
            )
        )


def resul_vales(request, vale_no, fecha, beneficiario, concepto, valor):

    ctx={
        'empresa':request.user.profile.empresa,
        'nit': request.user.profile.nit,
        'direccion': request.user.profile.direccion,
        'telefono': request.user.profile.telefono,
        'dian': request.user.profile.r_dian,
        'logo': request.user.profile.logo,
        'vale_no': vale_no,
        'fecha': fecha,
        'beneficiario': beneficiario,
        'concepto': concepto,
        'valor': valor
    }

    return render(request, "facturas/resul_vales.html", context={ 'ctx': ctx, 'fecha': fecha })




class EstadoCarteraList(LoginRequiredMixin, generic.ListView):
    login_url = 'generales:login'
    model=Facturas
    template_name="facturas/estado_cartera.html"
    context_object_name="cartera"

    def get(self, request, *args, **kwargs):
        cartera = Facturas.objects.filter(usuario=self.request.user, saldo_credito__gt=0).order_by('-factura')
        ctx = {}
        ctx['cartera'] = cartera
        self.object_list = ctx
        total=0
        for i, item in enumerate(cartera):
            total += item.saldo_credito
        return self.render_to_response(
            self.get_context_data(
                context = ctx,
                empresa = self.request.user.profile.empresa,
                total = total,
                hoy = datetime.now(tz=timezone.utc)
            )
        )




class InformePagosView(LoginRequiredMixin, generic.ListView):
    
    model = PagosCartera
    template_name = "facturas/info_pagos.html"
    context_object_name = "obj"
    login_url='generales:login'

    def get(self, request, *args, **kwargs):
        ano = int(request.GET.get('periodo')[0:4])
        mes = int(request.GET.get('periodo')[5:7])
        hoy = datetime.now(tz=timezone.utc)
        if not ano:
            ano = hoy.year
            mes = hoy.month
        pagos = PagosCartera.objects.filter(fecha__month=mes, fecha__year=ano, factura__usuario=request.user)
        context = {}
        context['mes'] = mes
        context['ano'] = ano
        context['empresa'] = request.user.profile.empresa
        context['pagos'] = pagos
        self.object_list = context

        return self.render_to_response(
            self.get_context_data(
                context = context
            )
        )







class InformeVentasMesView(LoginRequiredMixin, generic.ListView):
    
    model = Facturas
    template_name = "facturas/info_ventas.html"
    context_object_name = "obj"
    login_url='generales:login'

    def get(self, request, *args, **kwargs):
        ano = int(request.GET.get('periodo')[0:4])
        mes = int(request.GET.get('periodo')[5:7])
        ventas = Facturas.objects.filter(fecha_factura__month=mes, fecha_factura__year=ano, usuario=request.user)
        total=0
        if ventas:
            for i,item in enumerate(ventas):
                if item.anulado == False:
                    total += item.valor_factura

        context = {}
        context['mes'] = mes
        context['ano'] = ano
        context['total'] = total
        context['empresa'] = request.user.profile.empresa
        context['ventas'] = ventas
        self.object_list = context

        return self.render_to_response(
            self.get_context_data(
                context = context
            )
        )