from django import forms
from ckeditor.widgets import CKEditorWidget
import datetime
from generales.models import Contactos


class ContactosForm(forms.ModelForm):
    asunto = forms.CharField(max_length=100)
    nombre = forms.CharField(max_length=150)
    email = forms.EmailField(max_length=200)
    numero = forms.CharField(max_length=100)
    mensage = forms.CharField(widget=CKEditorWidget())

    class Meta:
        model=Contactos
        fields = ['asunto', 'nombre', 'email', 'numero', 'mensage', ]

    def __init__(self, *args, **kwargs):
        super().__init__(*args, **kwargs)
        for field in iter(self.fields):
            self.fields[field].widget.attrs.update({
                'class': 'form-control'
            })
    def clean_asunto(self):
        asunto = self.cleaned_data["asunto"]
        if not asunto:
            raise forms.ValidationError("Asunto requerido")
        return asunto

    def clean_nombre(self):
        nombre = self.cleaned_data["nombre"]
        if not nombre:
            raise forms.ValidationError("Nombre requerido")
        return nombre

    def clean_email(self):
        email = self.cleaned_data["email"]
        if not email:
            raise forms.ValidationError("eMail requerido")
        return email
    
    def clean_numero(self):
        numero = self.cleaned_data["numero"]
        if not numero:
            raise forms.ValidationError("Numero requerido")
        return numero
    
    def clean_mensage(self):
        mensage = self.cleaned_data["mensage"]
        if not mensage:
            raise forms.ValidationError("Mensage requerido")
        return mensage