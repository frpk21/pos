from django.contrib import admin
from .models import Ubicaciones
from django.contrib.admin.widgets import AutocompleteSelect


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

admin.site.register(Ubicaciones, UbicacionesAdmin)

