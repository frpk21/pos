from django.contrib import admin
from .models import Producto, Ubicaciones, Tipos_movimientos, Formulacion, Formulacion1
from django.contrib.admin.widgets import AutocompleteSelect

class Tipos_movimientosAdmin(admin.ModelAdmin):
    list_display = ('nombre', 'tipo', 'id', 'modificado', 'creado', 'activo',)
    ordering = ['tipo', 'nombre',]
    exclude = ['usuario',]
    filter_fields = ('tipo', )

    class Meta:
        model = Tipos_movimientos
    
    def save_model(self, request, obj, form, change):
        obj.usuario = request.user
        obj.save()
    
    def get_queryset(self, request):
        qs = Tipos_movimientos.objects.all()
        return qs.filter(usuario=request.user)
    
class UbicacionesAdmin(admin.ModelAdmin):
    list_display = ('id', 'descripcion', 'ciudad', 'direccion',)
    ordering = ['descripcion',]
    exclude = ['usuario',]
    search_fields = ('descripcion', )
    autocomplete_fields = ['ciudad',]

    class Meta:
        model = Ubicaciones
    
    def save_model(self, request, obj, form, change):
        obj.usuario = request.user
        obj.save()
    
    def get_queryset(self, request):
        qs = Ubicaciones.objects.all()
        return qs.filter(usuario=request.user)
  

"""class FormulacionAdmin(admin.ModelAdmin):
    list_display = ('nombre', 'producto', 'id',)
    #ordering = ['producto',]
    exclude = ['usuario',]
    search_fields = ('producto', )
    autocomplete_fields = ['producto',]

    class Meta:
        model = Formulacion
    
    def save_model(self, request, obj, form, change):
        obj.usuario = request.user
        obj.save()
    
    def get_queryset(self, request):
        qs = Formulacion.objects.all()
        return qs.filter(usuario=request.user)

admin.site.register(Formulacion, FormulacionAdmin)"""
admin.site.register(Ubicaciones, UbicacionesAdmin)
admin.site.register(Tipos_movimientos, Tipos_movimientosAdmin)


