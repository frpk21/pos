from django import forms

from django.forms.models import inlineformset_factory

from facturas.models import Facturas, Factp, Vales

from catalogos.models import Producto

from tempus_dominus.widgets import DatePicker, TimePicker, DateTimePicker

class FacturaPosEncForm(forms.ModelForm):
    valor_factura = forms.CharField()
    efectivo = forms.CharField()
    tdebito = forms.CharField()
    tcredito = forms.CharField()
    transferencia = forms.CharField()
    bonos = forms.CharField()

    class Meta:
        model=Facturas
        fields = ['fecha_factura','valor_factura','valor_iva','recibido','cambio', 'efectivo', 'tdebito', 'tcredito', 'transferencia', 'bonos','descuento',]

    def __init__(self, *args, **kwargs):
        super().__init__(*args, **kwargs)
        for field in iter(self.fields):
            self.fields[field].widget.attrs.update({
                'class': 'form-control'
            })
        self.fields['efectivo'].widget.attrs.update({'onkeyup': 'format(this)', 'style':'text-align: right; color:red; font-size:14px; width: 200px;'})
        self.fields['tdebito'].widget.attrs.update({'onkeyup': 'format(this)', 'style':'text-align: right; color:red; font-size:14px; width: 200px;'})
        self.fields['tcredito'].widget.attrs.update({'onkeyup': 'format(this)', 'style':'text-align: right; color:red; font-size:14px; width: 200px;'})
        self.fields['transferencia'].widget.attrs.update({'onkeyup': 'format(this)', 'style':'text-align: right; color:red; font-size:14px; width: 200px;'})
        self.fields['bonos'].widget.attrs.update({'onkeyup': 'format(this)', 'style':'text-align: right; color:red; font-size:14px; width: 200px;'})
        #self.fields['vales'].widget.attrs.update({'onkeyup': 'format(this)', 'style':'text-align: right; color:red; font-size:14px; width: 200px;'})
        self.fields['descuento'].widget.attrs.update({'onkeyup': 'format(this)', 'style':'text-align: right; color:red; font-size:14px; width: 200px;'})
        self.fields['efectivo'].required = False
        self.fields['tdebito'].required = False
        self.fields['tcredito'].required = False
        self.fields['transferencia'].required = False
        self.fields['bonos'].required = False
        #self.fields['vales'].required = False
        self.fields['descuento'].required = False
        self.fields['valor_iva'].required = False


class FacturaPosDetalleForm(forms.ModelForm):
    class Meta:
        model= Factp
        fields = [
            'factura',
            'codigo_de_barra',
            'cantidad',
            'producto',
            'valor_unidad',
            'porc_iva',
            'valor_iva',
            'valor_total',
            'prod'
            ]

    def __init__(self, *args, **kwargs):
        super().__init__(*args, **kwargs)
        for field in iter(self.fields):
            self.fields[field].widget.attrs.update({
                'class': 'form-control'
            })
        self.fields['codigo_de_barra'].widget.attrs['readonly']= True
        self.fields['producto'].widget.attrs['readonly']= True
        self.fields['cantidad'].widget.attrs['readonly']= True
        self.fields['valor_unidad'].widget.attrs['readonly']= True
        self.fields['valor_iva'].widget.attrs['readonly']= True
        self.fields['codigo_de_barra'].widget.attrs.update({'onkeydown':"onKeyDownHandler(event, id);"})
        #self.fields['codigo_de_barra'].widget.attrs.update({'onchange': 'validacbarra(id)'})          'onchange': 'validacbarra(id)', 
        #self.fields['cantidad'].widget.attrs.update({'onchange': 'validacantidad(id)', 'step':1})
        #self.fields['producto'].widget.attrs['readonly'] = 'readonly'
        #self.fields['producto'].widget.attrs.update({'style': 'color: blue; background: rgb(255, 255,255);'})
        #self.fields['total'].widget.attrs['readonly'] = 'readonly'
        self.fields['codigo_de_barra'].widget.attrs.update({'style': 'color: gray; background: rgb(255, 255,255);'})
        self.fields['producto'].widget.attrs.update({'style': 'color: blue; background: rgb(255, 255,255);'})
        self.fields['cantidad'].widget.attrs.update({'style': 'color: blue; background: rgb(255, 255,255);'})
        self.fields['valor_unidad'].widget.attrs.update({'style': 'color: blue; background: rgb(255, 255,255);'})
        self.fields['valor_iva'].widget.attrs.update({'style': 'color: blue; background: rgb(255, 255,255);'})
        self.fields['porc_iva'].widget = forms.HiddenInput()
        #self.fields['valor_iva'].widget = forms.HiddenInput()
        self.fields['valor_total'].widget = forms.HiddenInput()
        self.fields['prod'].widget = forms.HiddenInput()
        #self.fields['costo'].widget.attrs.update({'onchange': 'validacosto(id)'})

    def clean_cantidad(self):
        cantidad = self.cleaned_data["cantidad"]
        #if not cantidad:
        #    raise forms.ValidationError("Cantidad Requerida")
        #elif cantidad<=0:
        #    raise forms.ValidationError("Cantidad Incorrecta")
        return cantidad
    
    def clean_valor_unidad(self):
        valor_unidad = self.cleaned_data["valor_unidad"]
        #if not valor_unidad:
        #    raise forms.ValidationError("Precio Requerido")
        #if valor_unidad <= 0:
        #    raise forms.ValidationError("Precio Incorrecto")
        return valor_unidad
    
    def clean_codigo_de_barra(self):
        codigo_de_barra = self.cleaned_data["codigo_de_barra"]
        return codigo_de_barra

    def clean_porc_iva(self):
        porc_iva = self.cleaned_data["porc_iva"]
        return porc_iva
    
    def clean_valor_iva(self):
        valor_iva = self.cleaned_data["valor_iva"]
        return valor_iva
    
    def clean_valor_total(self):
        valor_total = self.cleaned_data["valor_total"]
        return valor_total
    
    def clean_prod(self):
        prod = self.cleaned_data["prod"]
        return prod


DetalleMovimientosFormSet = inlineformset_factory(Facturas,Factp,form=FacturaPosDetalleForm, extra=0,
    min_num=0,
    validate_min=True, can_delete=False)




class ValesForm(forms.ModelForm):
    valor = forms.CharField()

    class Meta:
        model = Vales
        fields = ['concepto','valor','beneficiario',]

    def __init__(self, *args, **kwargs):
        super().__init__(*args, **kwargs)
        for field in iter(self.fields):
            self.fields[field].widget.attrs.update({
                'class': 'form-control'
            })
        self.fields['valor'].widget.attrs.update({'onkeyup': 'format(this)', 'style':'text-align: right; color:red; font-size:14px; width: 200px;', 'onchange': 'validavalor(id)'})
    
    def clean_valor(self):
        valor = int(self.cleaned_data["valor"])
        if valor == 0:
            raise forms.ValidationError("Valor Requerido")
        elif valor < 0:
            raise forms.ValidationError("Valor Incorrecto")
        return valor

    def clean_concepto(self):
        concepto = self.cleaned_data["concepto"]
        if not concepto:
            raise forms.ValidationError("Concepto Requerido")
        return concepto

    def clean_beneficiario(self):
        beneficiario = self.cleaned_data["beneficiario"]
        if not beneficiario:
            raise forms.ValidationError("Beneficiario Requerido")
        return beneficiario



class FacturaEncForm(forms.ModelForm):    
    class Meta:
        model=Facturas
        fields = ['fecha_factura', 'observacion']

    def __init__(self, *args, **kwargs):
        super().__init__(*args, **kwargs)
        for field in iter(self.fields):
            self.fields[field].widget.attrs.update({
                'class': 'form-control'
            })

   


class FacturaDetForm(forms.ModelForm):
    producto = forms.ModelChoiceField(
        queryset=Producto.objects.filter(activo=True).
        order_by("descripcion"),
        empty_label="Seleccione Producto"
    )

    class Meta:
        model= Factp
        fields = [
            'producto',
            'cantidad',
            'porc_iva',
            'valor_iva',
            'valor_unidad',
            'descuento',
            'valor_total'
            ]

    def __init__(self, *args, **kwargs):
        super().__init__(*args, **kwargs)
        for field in iter(self.fields):
            self.fields[field].widget.attrs.update({
                'class': 'form-control'
            })
        self.fields['valor_total'].widget.attrs['readonly']= True

    def clean_cantidad(self):
        cantidad = self.cleaned_data["cantidad"]
        if not cantidad:
            raise forms.ValidationError("Cantidad Requerida")
        elif cantidad<=0:
            raise forms.ValidationError("Cantidad Incorrecta")
        return cantidad
    
    def clean_valor_unidad(self):
        valor_unidad = self.cleaned_data["valor_unidad"]
        if not valor_unidad:
            raise forms.ValidationError("Precio Requerido")
        if valor_unidad <= 0:
            raise forms.ValidationError("Precio Incorrecto")
        return valor_unidad


DetalleFacFormSet = inlineformset_factory(Facturas,Factp,form=FacturaDetForm, extra=4)