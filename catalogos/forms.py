from django import forms

from catalogos.models import Categoria, SubCategoria, Producto, Iva

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