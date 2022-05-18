from django.contrib import admin
from facturas.models import Facturas, Factp, FormasPagos, PagosCartera, Cierres, Cierres1, GrabadosCierres1, FormasPagosCierres1, Vales, PagosVales

class FacturasAdmin(admin.ModelAdmin):
    list_display = all()
    class Meta:
        model = Facturas
    
    def get_queryset(self, request):
        qs = Facturas.objects.all()
        return qs.filter(user=request.user)

class FactpAdmin(admin.ModelAdmin):
    list_display = all()
    class Meta:
        model = Factp
    
    def get_queryset(self, request):
        qs = Factp.objects.all()
        return qs.filter(user=request.user)

class FormasPagosAdmin(admin.ModelAdmin):
    list_display = all()
    class Meta:
        model = FormasPagos
    
    def get_queryset(self, request):
        qs = FormasPagos.objects.all()
        return qs.filter(user=request.user)

class PagosCarteraAdmin(admin.ModelAdmin):
    list_display = all()
    class Meta:
        model = PagosCartera
    
    def get_queryset(self, request):
        qs = PagosCartera.objects.all()
        return qs.filter(user=request.user)

class CierresAdmin(admin.ModelAdmin):
    list_display = all()
    class Meta:
        model = Cierres
    
    def get_queryset(self, request):
        qs = Cierres.objects.all()
        return qs.filter(user=request.user)

class Cierres1Admin(admin.ModelAdmin):
    list_display = all()
    class Meta:
        model = Cierres1
    
    def get_queryset(self, request):
        qs = Cierres1.objects.all()
        return qs.filter(user=request.user)

class GrabadosCierres1Admin(admin.ModelAdmin):
    list_display = all()
    class Meta:
        model = GrabadosCierres1
    
    def get_queryset(self, request):
        qs = GrabadosCierres1.objects.all()
        return qs.filter(user=request.user)

class FormasPagosCierres1Admin(admin.ModelAdmin):
    list_display = all()
    class Meta:
        model = FormasPagosCierres1
    
    def get_queryset(self, request):
        qs = FormasPagosCierres1.objects.all()
        return qs.filter(user=request.user)

class ValesAdmin(admin.ModelAdmin):
    list_display = all()
    class Meta:
        model = Vales
    
    def get_queryset(self, request):
        qs = Vales.objects.all()
        return qs.filter(user=request.user)

class PagosValesAdmin(admin.ModelAdmin):
    list_display = all()
    class Meta:
        model = PagosVales
    
    def get_queryset(self, request):
        qs = PagosVales.objects.all()
        return qs.filter(user=request.user)

# Register your models here.
admin.site.register(FacturasAdmin, Facturas)
admin.site.register(FactpAdmin, Factp)
admin.site.register(FormasPagosAdmin, FormasPagos)
admin.site.register(PagosCarteraAdmin, PagosCartera)
admin.site.register(CierresAdmin, Cierres)
admin.site.register(Cierres1Admin, Cierres1)
admin.site.register(GrabadosCierres1Admin, GrabadosCierres1)
admin.site.register(FormasPagosAdmin, FormasPagosCierres1)
admin.site.register(ValesAdmin, Vales)
admin.site.register(PagosValesAdmin, PagosVales)