from django.contrib import admin
from .models import Link
from django import forms
from django.forms import widgets
from social.models import Link


# Register your models here.

class SocialAdmin(admin.ModelAdmin):
    list_display = ('name', 'key', 'url')
    ordering = ('-name',)

    class Meta:
        model = Link

    def get_queryset(self, request):
        sitioR=request.META['HTTP_HOST']
        if sitioR=='127.0.0.1:8000':
            emisoraR=1
        elif sitioR=='cristalinacundinamarca.com':
            emisoraR=1
        elif sitioR=='cristalinahuila.com':
            emisoraR=2
        elif sitioR=='cristalinacaqueta.com':
            emisoraR=3
        elif sitioR=='hjdoblekhuila.com':
            emisoraR=4
        elif sitioR=='hjdoblekpitalito.com':
            emisoraR=5
        elif sitioR=='lacaquetena.net':
            emisoraR=6

        qs = Link.objects.all()
        return qs.filter(idEmisora=emisoraR)

    def save_model(self, request, obj, form, change):
        sitioR=request.META['HTTP_HOST']
        if sitioR=='127.0.0.1:8000':
            emisoraR=1
        elif sitioR=='cristalinacundinamarca.com':
            emisoraR=1
        elif sitioR=='cristalinahuila.com':
            emisoraR=2
        elif sitioR=='cristalinacaqueta.com':
            emisoraR=3
        elif sitioR=='hjdoblekhuila.com':
            emisoraR=4
        elif sitioR=='hjdoblekpitalito.com':
            emisoraR=5
        elif sitioR=='lacaquetena.net':
            emisoraR=6
        context = {'emisora': emisoraR}
      #  obj.idEmisora_id = (context['emisora'])
        super().save_model(request, obj, form, change)



admin.site.register(Link, SocialAdmin)
