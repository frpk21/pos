from django import forms

from django.forms.models import inlineformset_factory

from facturas.models import Facturas, Factp
from catalogos.models import Producto

from tempus_dominus.widgets import DatePicker, TimePicker, DateTimePicker




class FacturaEncForm(forms.ModelForm):
    fecha_factura = forms.DateField(widget=DatePicker())
    # forms.DateInput()
    fecha_factura = forms.DateTimeField(
        widget=DatePicker(
            options={
                'useCurrent': True,
                'collapse': True,
                'size': 'small',
                'language': 'es',
            },
            attrs={
                'append': 'fa fa-calendar',
                'icon_toggle': True,
            }
        ),
    )
    
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
        fields = ['producto', 'cantidad',
        'precio', 'total'
        ]

    def __init__(self, *args, **kwargs):
        super().__init__(*args, **kwargs)
        for field in iter(self.fields):
            self.fields[field].widget.attrs.update({
                'class': 'form-control'
            })
        self.fields['total'].widget.attrs['readonly']= True

    def clean_cantidad(self):
        cantidad = self.cleaned_data["cantidad"]
        if not cantidad:
            raise forms.ValidationError("Cantidad Requerida")
        elif cantidad<=0:
            raise forms.ValidationError("Cantidad Incorrecta")
        return cantidad
    
    def clean_precio(self):
        precio = self.cleaned_data["precio"]
        if not precio:
            raise forms.ValidationError("Precio Requerido")
        if precio <= 0:
            raise forms.ValidationError("Precio Incorrecto")
        return precio


DetalleFacFormSet = inlineformset_factory(Facturas,Factp,form=FacturaDetForm, extra=4)