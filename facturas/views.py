from django.http import HttpResponseRedirect
from django.urls import reverse_lazy
from django.shortcuts import render
from django.contrib.auth.mixins import LoginRequiredMixin
from django.views import generic
from django.contrib.messages.views import SuccessMessageMixin
from .models import Facturas, Factp
from .forms import FacturaEncForm, FacturaDetForm, DetalleFacFormSet
from generales.views import SinPrivilegios
from fact_digital.utilidades import facturaDian, consultaRangos, fechaDian, HoraDian, AnulaFactura
from django.http import JsonResponse
from django.core.exceptions import ValidationError
from django.core.validators import validate_email
from pos.utilidades import enviar_mail, numero_to_letras
from datetime import date
from datetime import datetime, timedelta
from django.db.models import Sum





class FacturaList(LoginRequiredMixin, generic.ListView):
    login_url = 'generales:login'
    model=Facturas
    template_name="salidas/facturas_list.html"
    context_object_name="facturas"






class FacturaNew(LoginRequiredMixin, generic.CreateView):
    permission_required =  'facturas.add_facturas'
    model = Facturas
    login_url = 'generales:home'
    template_name = 'salidas/factura_form.html'
    form_class = FacturaEncForm
    success_url = reverse_lazy('salidas:factura_list')

    def get(self, request, *args, **kwargs): 
        self.object = None
        form_class = self.get_form_class()
        form = self.get_form(form_class)
        detalle_factura_formset = DetalleFacFormSet()
        return self.render_to_response(
            self.get_context_data(
                form=form,
                detalle_factura=detalle_factura_formset
            )
        )

    def post(self, request, *args, **kwargs):
        form_class = self.get_form_class()
        form = self.get_form(form_class)
        detalle_factura = DetalleFacFormSet(request.POST)

        if form.is_valid() and detalle_factura.is_valid():
            return self.form_valid(form, detalle_factura)
        else:
            return self.form_invalid(form, detalle_factura)

    def form_valid(self, form, detalle_factura):
        cons_fact = consultaRangos()
        facturanew = int(cons_fact['generalResult']['ranges'][2]['last'])+1
        self.object = form.save(commit=False)
        try:
            validate_email(self.object.contrato.nit_cliente.email.strip())
        except ValidationError as e:
            return self.form_invalid(form, detalle_factura)
        else:
            self.object.factura = facturanew
            self.object.user = self.request.user
            self.object.sede = self.request.user.profile.sede
            self.object = form.save()
            detalle_factura.instance = self.object
            detalle_factura.factura = facturanew
            detalle_factura.save()
            tipo_entidad = self.object.contrato.nit_cliente.tipo_entidad
            if tipo_entidad==1:
                tipo_entidad=2
            else:
                tipo_entidad=1
            data_dian = {}
            data_dian['rangeKey'] = cons_fact['generalResult']['ranges'][2]['rangeKey']
            data_dian['intID'] = self.object.id
            data_dian['issueDate'] = fechaDian(self.object.fecha_factura)
            data_dian['issueTime'] = HoraDian()
            data_dian['dueDate'] = fechaDian(self.object.fecha_factura+timedelta(days=30))
            data_dian['note1'] = numero_to_letras(self.object.saldo)
            data_dian['note2'] = self.object.observacion
            data_dian['additionalAccountID'] = str(tipo_entidad)
            data_dian['name'] = self.object.contrato.nit_cliente.rzn_social
            data_dian['city'] = self.object.contrato.nit_cliente.ciudad.nombre_ciudad
            data_dian['countryEntity'] = str(self.object.contrato.nit_cliente.ciudad.departamento.cod_dane).strip()
            data_dian['countrySubentity'] = str(self.object.contrato.nit_cliente.ciudad.cod_dane).strip()
            data_dian['addressLine'] = self.object.contrato.nit_cliente.direccion
            data_dian['documentNumber'] = str(self.object.contrato.nit_cliente.nit).strip()
            data_dian['documentType'] = str(self.object.contrato.nit_cliente.tipo_nit).strip()
            data_dian['telephone'] = self.object.contrato.nit_cliente.tel1
            data_dian['email'] = self.object.contrato.nit_cliente.email
            data_dian['totalAmount'] =  str(self.object.valor_factura)
            data_dian['discountAmount'] = "0.00"
            data_dian['extraAmount'] = "0.00"
            data_dian['taxAmount'] = str(self.object.valor_iva)
            data_dian['payAmount'] = str(self.object.saldo)
            data_dian['unitPrice'] = str(self.object.valor_factura)
            data_dian['total'] = str(self.object.valor_factura)
            data_dian['description'] = "SERVICIO DE PUBLICIDAD SEGUN ORDEN # " + self.object.contrato.no_publicidad
            data_dian['brand'] = "INRAI"
            data_dian['model'] = "N/A"
            data_dian['ID'] = "01"  # 01 = IVA   02=ICA   03=consumo    04=bolsas   22=bolsas
            r = facturaDian(data_dian)
            if r['invoiceResult']['status']['code'] == 200:
                return JsonResponse(
                    {
                        'content': {
                            'message': 'Generada la Factura No. '+str(facturanew)+' exitosamente.'
                        }
                    }
                )
            else:
                Factp.objects.filter(factura=facturanew).delete()
                Facturas.objects.filter(factura=facturanew).delete()
                return JsonResponse(
                    {
                        'content': {
                            'message': 'ERROR en la Factura No. '+str(facturanew)+', no fue generada (por favor corrija y vuelva a intentarlo). >>ERROR = '+str(r)
                        }
                    }
                )

    def form_invalid(self, form, detalle_factura):
        return self.render_to_response(
            self.get_context_data(
                form=form,
                detalle_factura=detalle_factura
            )
        )


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
