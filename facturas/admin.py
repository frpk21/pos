from django.contrib import admin

from facturas.models import Facturas, Factp, FormasPagos, PagosCartera, Cierres, Cierres1, GrabadosCierres1, FormasPagosCierres1, Vales, PagosVales


class FacturasAdmin(admin.ModelAdmin):
    def get_list_display(self, request):
        return [field.name for field in self.model._meta.concrete_fields]

    class Meta:
        model = Facturas
    
    def get_queryset(self, request):
        qs = Facturas.objects.all()
        return qs.filter(user=request.user)

class FactpAdmin(admin.ModelAdmin):
    def get_list_display(self, request):
        return [field.name for field in self.model._meta.concrete_fields]
    
    class Meta:
        model = Factp
    
    def get_queryset(self, request):
        qs = Factp.objects.all()
        return qs.filter(user=request.user)

class FormasPagosAdmin(admin.ModelAdmin):
    def get_list_display(self, request):
        return [field.name for field in self.model._meta.concrete_fields]
    
    class Meta:
        model = FormasPagos
    
    def get_queryset(self, request):
        qs = FormasPagos.objects.all()
        return qs.filter(user=request.user)

class PagosCarteraAdmin(admin.ModelAdmin):
    def get_list_display(self, request):
        return [field.name for field in self.model._meta.concrete_fields]
    
    class Meta:
        model = PagosCartera
    
    def get_queryset(self, request):
        qs = PagosCartera.objects.all()
        return qs.filter(user=request.user)

class CierresAdmin(admin.ModelAdmin):
    def get_list_display(self, request):
        return [field.name for field in self.model._meta.concrete_fields]
    
    class Meta:
        model = Cierres
    
    def get_queryset(self, request):
        qs = Cierres.objects.all()
        return qs.filter(user=request.user)

class Cierres1Admin(admin.ModelAdmin):
    def get_list_display(self, request):
        return [field.name for field in self.model._meta.concrete_fields]
    
    class Meta:
        model = Cierres1
    
    def get_queryset(self, request):
        qs = Cierres1.objects.all()
        return qs.filter(user=request.user)

class GrabadosCierres1Admin(admin.ModelAdmin):
    def get_list_display(self, request):
        return [field.name for field in self.model._meta.concrete_fields]
    
    class Meta:
        model = GrabadosCierres1
    
    def get_queryset(self, request):
        qs = GrabadosCierres1.objects.all()
        return qs.filter(user=request.user)

class FormasPagosCierres1Admin(admin.ModelAdmin):
    def get_list_display(self, request):
        return [field.name for field in self.model._meta.concrete_fields]
    
    class Meta:
        model = FormasPagosCierres1
    
    def get_queryset(self, request):
        qs = FormasPagosCierres1.objects.all()
        return qs.filter(user=request.user)

class ValesAdmin(admin.ModelAdmin):
    def get_list_display(self, request):
        return [field.name for field in self.model._meta.concrete_fields]
    
    class Meta:
        model = Vales
    
    def get_queryset(self, request):
        qs = Vales.objects.all()
        return qs.filter(user=request.user)

class PagosValesAdmin(admin.ModelAdmin):
    class Meta:
        model = PagosVales
    
    def get_queryset(self, request):
        qs = PagosVales.objects.all()
        return qs.filter(user=request.user)

admin.site.register(Facturas)
admin.site.register(Factp)
admin.site.register(FormasPagos)
admin.site.register(PagosCartera)
admin.site.register(Cierres)
admin.site.register(Cierres1)
admin.site.register(GrabadosCierres1)
admin.site.register(FormasPagosCierres1)
admin.site.register(Vales)
admin.site.register(PagosVales)