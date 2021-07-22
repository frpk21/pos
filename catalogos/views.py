from django.shortcuts import render

from django.views import generic

from catalogos.models import Categoria, SubCategoria, Producto, Iva

from generales.models import Terceros

from django.db import connections

from collections import namedtuple

from catalogos.forms import CategoriaForm, SubCategoriaForm, ProductoForm, IvaForm

from django.urls import reverse_lazy

from django.contrib.auth.mixins import LoginRequiredMixin, PermissionRequiredMixin

from generales.views import SinPrivilegios

from django.contrib.messages.views import SuccessMessageMixin

from django.http import JsonResponse

from django.http import HttpResponseRedirect


class CategoriaView(LoginRequiredMixin, generic.ListView):
    model = Categoria
    template_name = "catalogos/categorias.html"
    context_object_name = "obj"
    login_url='generales:login'


class CategoriaNew(SuccessMessageMixin, LoginRequiredMixin, SinPrivilegios, generic.CreateView):
    permission_required='catalogos.add_categoria'
    model=Categoria
    template_name="catalogos/categorias_form.html"
    context_object_name='obj1'
    form_class=CategoriaForm
    success_url=reverse_lazy("catalogos:categoria_list")
    success_message='Categoría creada satisfactoriamente'
#    login_url='generales:login'


class CategoriaEdit(LoginRequiredMixin, SinPrivilegios, generic.UpdateView):
    permission_required='catalogos.change_categoria'
    model=Categoria
    template_name="catalogos/categorias_form.html"
    context_object_name='obj2'
    form_class=CategoriaForm
    success_url=reverse_lazy("catalogos:categoria_list")
#    login_url='generales:login'


class CategoriaDel(LoginRequiredMixin, SinPrivilegios, generic.DeleteView):
    permission_required='catalogos.delete_categoria'
    model=Categoria
    template_name="catalogos/categorias_del.html"
    context_object_name='obj3'
    form_class=CategoriaForm
    success_url=reverse_lazy("catalogos:categoria_list")
#    login_url='generales:login'


class SubCategoriaView(LoginRequiredMixin, generic.ListView):
    model = SubCategoria
    template_name = "catalogos/subcategorias.html"
    context_object_name = "obj"
    login_url='generales:login'

class SubCategoriaNew(SuccessMessageMixin, LoginRequiredMixin, SinPrivilegios, generic.CreateView):
    permission_required='catalogos.add_subcategoria'
    model=SubCategoria
    template_name="catalogos/subcategorias_form.html"
    context_object_name='obj1'
    form_class=SubCategoriaForm
    success_url=reverse_lazy("catalogos:subcategoria_list")
    success_message='Sub Categoría creada satisfactoriamente'

class SubCategoriaEdit(SuccessMessageMixin,SinPrivilegios, generic.UpdateView):
    permission_required='catalogos.change_subcategoria'
    model=SubCategoria
    template_name="catalogos/subcategorias_form.html"
    context_object_name='obj'
    form_class=SubCategoriaForm
    success_url=reverse_lazy("catalogos:subcategoria_list")
    success_message='Sub Categoría modificada satisfactoriamente'

class SubCategoriaDel(LoginRequiredMixin, SinPrivilegios, generic.DeleteView):
    permission_required='catalogos.delete_subcategoria'
    model=SubCategoria
    template_name="catalogos/subcategorias_del.html"
    context_object_name='obj'
    form_class=SubCategoriaForm
    success_url=reverse_lazy("catalogos:subcategoria_list")


class ProductoView(LoginRequiredMixin, generic.ListView):
    model = Producto
    template_name = "catalogos/producto_list.html"
    context_object_name = "obj"
    login_url='generales:login'

    def get_queryset(self):
        return Producto.objects.filter(usuario=self.request.user)
    

class ProductoNew(SuccessMessageMixin, LoginRequiredMixin, SinPrivilegios, generic.CreateView):
    permission_required='catalogos.add_producto'
    model = Producto
    template_name="catalogos/productos_form.html"
    context_object_name = 'obj'
    form_class = ProductoForm
    success_url = reverse_lazy("catalogos:productos_list")
    success_message='Producto creado satisfactoriamente'

    def get(self, request, *args, **kwargs): 
        self.object = None
        form_class = self.get_form_class()
        form = self.get_form(form_class)

        return self.render_to_response(
            self.get_context_data(
                form=form
            )
        )

    def post(self, request, *args, **kwargs):
        form_class = self.get_form_class()
        form = self.get_form(form_class)
        #print("XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX")
        #print(form.errors)
        if form.is_valid():
            return self.form_valid(form)
        else:
            return self.form_invalid(form)

    def form_valid(self, form):
        self.object = form.save(commit=False)
        self.object.usuario = self.request.user
        self.object = form.save()
        return HttpResponseRedirect(self.success_url)

    def form_invalid(self, form):
        self.object = form
        return self.render_to_response(
            self.get_context_data(
                form=form
            )
        )



class ProductoEdit(SuccessMessageMixin,SinPrivilegios, generic.UpdateView):
    permission_required = 'catalogos.change_producto'
    model = Producto
    template_name = "catalogos/productos_form.html"
    context_object_name='obj'
    form_class=ProductoForm
    success_url=reverse_lazy("catalogos:productos_list")
    success_message='Producto actualizado satisfactoriamente'

class ProductoDel(LoginRequiredMixin, SinPrivilegios, generic.DeleteView):
    permission_required='catalogos.delete_producto'
    model=Producto
    template_name="catalogos/producto_del.html"
    context_object_name='obj'
    form_class=ProductoForm
    success_url=reverse_lazy("catalogos:productos_list")

class TarifasIvaListView(LoginRequiredMixin, generic.ListView):
    model = Iva
    template_name = "catalogos/iva_list.html"
    context_object_name = "obj"
    login_url='generales:login'

class TarifaIvaNew(SuccessMessageMixin, LoginRequiredMixin, SinPrivilegios, generic.CreateView):
    permission_required='catalogos.add_iva'
    model = Iva
    template_name="catalogos/iva_form.html"
    context_object_name = 'obj'
    form_class = IvaForm
    success_url = reverse_lazy("catalogos:tarifas_iva")
    success_message='Producto creado satisfactoriamente'

class TarifaIvaEdit(SuccessMessageMixin,SinPrivilegios, generic.UpdateView):
    permission_required = 'catalogos.change_iva'
    model = Iva
    template_name = "catalogos/iva_form.html"
    context_object_name='obj'
    form_class=IvaForm
    success_url=reverse_lazy("catalogos:tarifas_iva")
    success_message='Producto actualizado satisfactoriamente'

class TarifaIvaDel(LoginRequiredMixin, SinPrivilegios, generic.DeleteView):
    permission_required='catalogos.delete_iva'
    model = Iva
    template_name="catalogos/iva_del.html"
    context_object_name='obj'
    form_class=IvaForm
    success_url=reverse_lazy("catalogos:tarifas_iva")


def get_ajaxSubcategoria(request, *args, **kwargs): 
    query = request.GET.get('q', None)
    if query: 
        terceros = SubCategoria.objects.filter(nombre__icontains=query).values("id","nombre") 
        terceros = list(terceros)
        return JsonResponse(terceros, safe=False) 
    else: 
        return JsonResponse(data={'success': False, 'errors': 'No encuentro resultados.'}) 


def get_ajaxTerceros(request, *args, **kwargs): 
    query = request.GET.get('q', None)
    if query: 
        terceros = Terceros.objects.filter(rzn_social__icontains=query).values("id","rzn_social") 
        terceros = list(terceros)
        return JsonResponse(terceros, safe=False) 
    else: 
        return JsonResponse(data={'success': False, 'errors': 'No encuentro resultados.'}) 
