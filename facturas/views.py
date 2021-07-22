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
