from django.contrib import admin
from generales.models import Profile, Terceros, Ciudad, Pais, Departamento
from django.contrib.admin.widgets import AutocompleteSelect


class CiudadAdmin(admin.ModelAdmin):
    list_display = ('nombre_ciudad', 'cod_dane', 'departamento')
    ordering = ('nombre_ciudad', 'departamento')
    search_fields = ('nombre_ciudad', )
    list_filter = ('departamento', 'nombre_ciudad')

    class Meta:
        model = Ciudad

class TercerosAdmin(admin.ModelAdmin):
    
    list_display = ('rzn_social', 'nombre1', 'nombre2', 'apellido1', 'apellido2', 'nit', 'email',
                    'tel1', 'tel2','celular','direccion')
    ordering = ('rzn_social', 'nombre1', 'apellido1')
    search_fields = ['rzn_social','nombre1','nombre2','apellido1']
    list_filter = ('ciudad',)
    autocomplete_fields = ['ciudad',]

    class Meta:
        model = Terceros
    
    def save_model(self, request, obj, form, change):
        obj.user = request.user
        obj.save()
    
    def get_queryset(self, request):
        qs = Terceros.objects.all()
        return qs.filter(user=request.user)

class ProfileAdmin(admin.ModelAdmin):
    list_display = ('user', 'foto', 'nit', 'empresa',)
    ordering = ['user',]
    search_fields = ('user', )

    class Meta:
        model = Profile
    
    def get_queryset(self, request):
        qs = Profile.objects.all()
        return qs.filter(user=request.user)


admin.site.register(Terceros, TercerosAdmin)
admin.site.register(Profile, ProfileAdmin)
admin.site.register(Pais)
admin.site.register(Departamento)
admin.site.register(Ciudad, CiudadAdmin)
