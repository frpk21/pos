from django.shortcuts import render

from django.views import generic

from catalogos.models import Categoria, SubCategoria, Producto

from django.db import connections

from collections import namedtuple

from catalogos.forms import CategoriaForm, SubCategoriaForm, ProductoForm

from django.urls import reverse_lazy

from django.contrib.auth.mixins import LoginRequiredMixin, PermissionRequiredMixin

from generales.views import SinPrivilegios

from django.contrib.messages.views import SuccessMessageMixin


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

class ProductoNew(SuccessMessageMixin, LoginRequiredMixin, SinPrivilegios, generic.CreateView):
    permission_required='catalogos.add_producto'
    model = Producto
    template_name="catalogos/productos_form.html"
    context_object_name = 'obj'
    form_class = ProductoForm
    success_url = reverse_lazy("catalogos:productos_list")
    success_message='Producto creado satisfactoriamente'

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