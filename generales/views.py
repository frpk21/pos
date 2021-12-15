from django.shortcuts import render

from django.http import HttpResponse, HttpResponseRedirect

from django.urls import reverse_lazy

from django.views import generic

from django.contrib.auth.mixins import LoginRequiredMixin, PermissionRequiredMixin

from django.urls import reverse, reverse_lazy

from django.http import JsonResponse

from django.conf import settings

from django.contrib.auth.models import User

from generales.models import Profile


class SinPrivilegios(PermissionRequiredMixin):
    login_url='generales:sin_privilegios'
    raise_exception=False
    redirect_field_name="redirecto_to"

    def handle_no_permission(self):
        return HttpResponseRedirect(reverse_lazy(self.login_url))


class HomePage(generic.View):
    def get(self, request, *args, **kwargs):
        
        return HttpResponse('Pagina de Inicio')

class Home(generic.TemplateView):
    template_name='generales/home.html'
    login_url='generales:home'
    
    def get(self, request, *args, **kwargs): 
        if self.request.user.is_staff:
            cliente = Profile.objects.filter(user = self.request.user)
        else:
            cliente = None

        return self.render_to_response(
            self.get_context_data(
                cliente=cliente
            )
        )

class HomeSinPrivilegios(generic.TemplateView):
    template_name="generales/msg_sin_privilegios.html"