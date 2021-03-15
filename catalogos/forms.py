from django import forms

from catalogos.models import Categoria, SubCategoria, Producto

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

    class Meta:
        model = Producto
        fields = '__all__'

    def __init__(self,*args,**kwargs):
        super().__init__(*args,**kwargs)
        for field in iter(self.fields):
            self.fields[field].widget.attrs.update({
                'class': 'form-control'
            })