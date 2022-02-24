from django import forms

from django.forms.models import inlineformset_factory

from facturas.models import Facturas, Factp

from catalogos.models import Producto

from tempus_dominus.widgets import DatePicker, TimePicker, DateTimePicker

class FacturaPosEncForm(forms.ModelForm):
    valor_documento = forms.CharField()
    class Meta:
        model=Facturas

        fields = ['fecha_factura',]

    def __init__(self, *args, **kwargs):
        super().__init__(*args, **kwargs)
        for field in iter(self.fields):
            self.fields[field].widget.attrs.update({
                'class': 'form-control'
            })


class FacturaPosDetalleForm(forms.ModelForm):
    class Meta:
        model= Factp
        fields = [
            'codigo_de_barra',
            'cantidad',
            'producto',
            'valor_unidad',
            ]

    def __init__(self, *args, **kwargs):
        super().__init__(*args, **kwargs)
        for field in iter(self.fields):
            self.fields[field].widget.attrs.update({
                'class': 'form-control'
            })
        self.fields['producto'].widget.attrs['readonly']= True
        self.fields['cantidad'].widget.attrs['readonly']= True
        self.fields['valor_unidad'].widget.attrs['readonly']= True
        self.fields['codigo_de_barra'].widget.attrs.update({'onchange': 'validacbarra(id)'})
        #self.fields['codigo_de_barra'].widget.attrs.update({'onchange': 'validacbarra(id)'})
        #self.fields['cantidad'].widget.attrs.update({'onchange': 'validacantidad(id)', 'step':1})
        #self.fields['producto'].widget.attrs['readonly'] = 'readonly'
        #self.fields['producto'].widget.attrs.update({'style': 'color: blue; background: rgb(255, 255,255);'})
        #self.fields['total'].widget.attrs['readonly'] = 'readonly'
        self.fields['producto'].widget.attrs.update({'style': 'color: blue; background: rgb(255, 255,255);'})
        self.fields['cantidad'].widget.attrs.update({'style': 'color: blue; background: rgb(255, 255,255);'})
        self.fields['valor_unidad'].widget.attrs.update({'style': 'color: blue; background: rgb(255, 255,255);'})
        #self.fields['costo'].widget.attrs.update({'onchange': 'validacosto(id)'})

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
    
    def clean_codigo_de_barra(self):
        codigo_de_barra = self.cleaned_data["codigo_de_barra"]
        if not codigo_de_barra:
            raise forms.ValidationError("Codigo de Barra Requerido.")
        return codigo_de_barra
    


DetalleMovimientosFormSet = inlineformset_factory(Facturas,Factp,form=FacturaPosDetalleForm, extra=1,
    min_num=0,
    validate_min=True, can_delete=False)









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