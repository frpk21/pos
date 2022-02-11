from django import forms

from catalogos.models import Categoria, SubCategoria, Producto, Iva, Movimientos, Movimientos_detalle, Formulacion, Formulacion1

from django.forms.models import inlineformset_factory

class CategoriaForm(forms.ModelForm):
    class Meta:
        model=Categoria
        fields = ['nombre', 'activo'] 
        labels = {'nombre': "Descripción de la Catagoría",
                  'activo': "Estado"}
        widget = {'nombre': forms.TextInput()}

    def __init__(self,*args,**kwargs):
        super().__init__(*args,**kwargs)
        for field in iter(self.fields):
            self.fields[field].widget.attrs.update({
                'class': 'form-control'
            })




class SubCategoriaForm(forms.ModelForm):
    categoria=forms.ModelChoiceField(
        queryset=Categoria.objects.filter(activo=True).
        order_by('nombre')
    )
    class Meta:
        model=SubCategoria
        fields = ['categoria','nombre', 'activo'] 
        labels = {'categoria': "Categoría",
                  'nombre': "Descripción de la Catagoría",
                  'activo': "Estado"}
        widget = {'nombre': forms.TextInput()}

    def __init__(self,*args,**kwargs):
        super().__init__(*args,**kwargs)
        for field in iter(self.fields):
            self.fields[field].widget.attrs.update({
                'class': 'form-control'
            })
        self.fields['categoria'].empty_label="Seleccione un categería:"






class ProductoForm(forms.ModelForm):
    subcategoria = forms.ModelChoiceField(
        queryset = SubCategoria.objects.filter(activo=True).
        order_by('categoria__nombre','nombre'),
        empty_label = "Seleccione una sub categoría"
    )
    archivo_foto = forms.FileField(required=False)

    class Meta:
        model = Producto
        fields = ['subcategoria','nombre','descripcion','archivo_foto','unidad_de_medida','proveedor','stock_minimo',\
            'stock_maximo','costo_unidad','tarifa_iva','precio_de_venta','codigo_de_barra','cuenta_contable_ventas_locales',\
                'cuenta_contable_ventas_exterior', 'ubicacion']
        widget = {'descripcion': forms.TextInput()}    

    def __init__(self,*args,**kwargs):
        super().__init__(*args,**kwargs)
        for field in iter(self.fields):
            self.fields[field].widget.attrs.update({
                'class': 'form-control'
            })

    def clean_subcategoria(self):
        subcategoria = self.cleaned_data["subcategoria"]
        if not subcategoria:
            raise forms.ValidationError("Subcategoria Requerida")
        return subcategoria

    def clean_nombre(self):
        nombre = self.cleaned_data["nombre"]
        if not nombre:
            raise forms.ValidationError("Nombre Requerido")
        return nombre

    def clean_costo_unidad(self):
        costo_unidad = self.cleaned_data["costo_unidad"]
        if not costo_unidad:
            raise forms.ValidationError("Costo Requerido")
        if costo_unidad <= 0:
            raise forms.ValidationError("Costo Incorrecto")
        return costo_unidad

    def clean_precio_de_venta(self):
        precio_de_venta = self.cleaned_data["precio_de_venta"]
        if not precio_de_venta:
            raise forms.ValidationError("Precio de Venta Requerido")
        if precio_de_venta <= 0:
            raise forms.ValidationError("Precio de Venta Incorrecto")
        return precio_de_venta






class IvaForm(forms.ModelForm):
    class Meta:
        model = Iva
        fields = ['tarifa_iva',]

    def __init__(self,*args,**kwargs):
        super().__init__(*args,**kwargs)
        for field in iter(self.fields):
            self.fields[field].widget.attrs.update({
                'class': 'form-control'
            })
            
            
            
class MovimientosEncForm(forms.ModelForm):
    valor_documento = forms.CharField()
    class Meta:
        model=Movimientos
        fields = ['fecha', 'tipo', 'tercero', 'ubicacion','valor_documento',]

    def __init__(self, *args, **kwargs):
        super().__init__(*args, **kwargs)
        for field in iter(self.fields):
            self.fields[field].widget.attrs.update({
                'class': 'form-control'
            })
        self.fields['valor_documento'].widget.attrs['readonly'] = 'readonly'
        self.fields['valor_documento'].widget.attrs.update({'style': 'color: blue; background: rgb(255, 255,255);'})

    def clean_fecha(self):
        fecha = self.cleaned_data["fecha"]
        if not fecha:
            raise forms.ValidationError("Campo fecha es obligatorio.")
        return fecha

    def clean_tipo(self):
        tipo = self.cleaned_data["tipo"]
        if not tipo:
            raise forms.ValidationError("Tipo de documento requerido.")
        return tipo

    def clean_tercero(self):
        tercero = self.cleaned_data["tercero"]
        if not tercero:
            raise forms.ValidationError("Proveedor requerido.")
        return tercero

   


class MovimientosDetForm(forms.ModelForm):
    costo = forms.CharField()
    total = forms.CharField()
    producto = forms.CharField()
    class Meta:
        model = Movimientos_detalle
        fields = ['codigo_de_barra', 'producto', 'cantidad', 'costo', 'total'  ]

    def __init__(self, *args, **kwargs):
        super().__init__(*args, **kwargs)
        for field in iter(self.fields):
            self.fields[field].widget.attrs.update({
                'class': 'form-control'
            })
        self.fields['codigo_de_barra'].widget.attrs.update({'onchange': 'validacbarra(id)'})
        self.fields['cantidad'].widget.attrs.update({'onchange': 'validacantidad(id)', 'step':1})
        self.fields['producto'].widget.attrs['readonly'] = 'readonly'
        self.fields['producto'].widget.attrs.update({'style': 'color: blue; background: rgb(255, 255,255);'})
        self.fields['total'].widget.attrs['readonly'] = 'readonly'
        self.fields['total'].widget.attrs.update({'style': 'color: red; background: rgb(255, 255,255);'})
        self.fields['costo'].widget.attrs.update({'onchange': 'validacosto(id)'})
        #self.fields['cantidad'].widget.attrs.update({'class': 'text-right form-control'})
        #self.fields['costo'].widget.attrs.update({'type': 'text', 'class': 'text-right form-control'})
        
    def clean_cantidad(self):
        cantidad = self.cleaned_data["cantidad"]
        if not cantidad:
            raise forms.ValidationError("Cantidad Requerida.")
        return cantidad
    
    def clean_costo(self):
        costo = self.cleaned_data["costo"]
        if not costo:
            raise forms.ValidationError("Costo Requerido.")
        return costo
        
    def clean_codigo_de_barra(self):
        codigo_de_barra = self.cleaned_data["codigo_de_barra"]
        if not codigo_de_barra:
            raise forms.ValidationError("Codigo de Barra Requerido.")
        return codigo_de_barra

DetalleMovimientosFormSet = inlineformset_factory(Movimientos,Movimientos_detalle,form=MovimientosDetForm, extra=1,
    min_num=0,
    validate_min=True, can_delete=False)




class FormulacionEncForm(forms.ModelForm):
    class Meta:
        model=Formulacion
        fields = ['producto', 'nombre',]

    def __init__(self, *args, **kwargs):
        super().__init__(*args, **kwargs)
        for field in iter(self.fields):
            self.fields[field].widget.attrs.update({
                'class': 'form-control'
            })

    def clean_producto(self):
        producto = self.cleaned_data["producto"]
        if not producto:
            raise forms.ValidationError("Debe seleccionar un producto.")
        return producto

    def clean_nombre(self):
        nombre = self.cleaned_data["nombre"]
        if not nombre:
            raise forms.ValidationError("Nombre formula requerido.")
        return nombre


class FormulacionDetForm(forms.ModelForm):
    class Meta:
        model = Formulacion1
        fields = ['producto', 'cantidad',]

    def __init__(self, *args, **kwargs):
        super().__init__(*args, **kwargs)
        for field in iter(self.fields):
            self.fields[field].widget.attrs.update({
                'class': 'form-control'
            })
        
    def clean_producto(self):
        producto = self.cleaned_data["producto"]
        if not producto:
            raise forms.ValidationError("Producto Requerido.")
        return producto
    
    def clean_cantidad(self):
        cantidad = self.cleaned_data["cantidad"]
        if not cantidad:
            raise forms.ValidationError("Cantidad Requerida.")
        return cantidad

DetalleFormulacionFormSet = inlineformset_factory(Formulacion, Formulacion1, form=FormulacionDetForm, extra=0,
    min_num=0,
    validate_min=True, can_delete=False)