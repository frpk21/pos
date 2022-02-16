from datetime import datetime
from unicodedata import decimal
from django.shortcuts import render

from django.views import generic

from catalogos.models import Categoria, SubCategoria, Producto, Iva, Movimientos, Formulacion, Tipos_movimientos

from generales.models import Terceros, Profile

from django.db import connections

from collections import namedtuple

from catalogos.forms import CategoriaForm, SubCategoriaForm, ProductoForm, IvaForm, MovimientosEncForm, DetalleMovimientosFormSet, FormulacionEncForm, FormulacionDetForm, DetalleFormulacionFormSet

from django.urls import reverse_lazy

from django.contrib.auth.mixins import LoginRequiredMixin, PermissionRequiredMixin

from generales.views import SinPrivilegios

from django.contrib.messages.views import SuccessMessageMixin

from django.http import JsonResponse

from django.http import HttpResponseRedirect

from datetime import date

from datetime import datetime, timedelta

from django.db.models import Sum

from facturas.conectorplugin import Conector, AccionBarcodeJan13, AlineacionCentro


def MenuView(request, *args, **kwargs):
    template_name="catalogos/menu.html"
    context={'hoy': date.today()}
   
    return render(request, template_name, context)

def MenuInvView(request, *args, **kwargs):
    template_name="catalogos/menu_inv.html"
    context={'hoy': date.today()}
   
    return render(request, template_name, context)


class CategoriaView(LoginRequiredMixin, generic.ListView):
    model = Categoria
    template_name = "catalogos/categorias.html"
    context_object_name = "obj"
    login_url='generales:login'
    
    def get_queryset(self):
        return Categoria.objects.filter(usuario=self.request.user)


class CategoriaNew(SuccessMessageMixin, LoginRequiredMixin, SinPrivilegios, generic.CreateView):
    permission_required='catalogos.add_categoria'
    model=Categoria
    template_name="catalogos/categorias_form.html"
    context_object_name='obj1'
    form_class=CategoriaForm
    success_url=reverse_lazy("catalogos:categoria_list")
    success_message='Categoría creada satisfactoriamente'
#    login_url='generales:login'
    def post(self, request, *args, **kwargs):
        form = CategoriaForm(request.POST)
        if form.is_valid():
            return self.form_valid(form)
        else:
            return self.form_invalid(form)

    def form_valid(self, form):
        self.object = form.save(commit=False)
        self.object.usuario = self.request.user
        self.object = form.save()
        
        return HttpResponseRedirect(reverse_lazy("catalogos:categoria_list"))


    def form_invalid(self, form, detalle_movimientos, tipor):
        self.object=form
        return self.render_to_response(
            self.get_context_data(
                form=form,
                detalle_movimientos=detalle_movimientos
            )
        )


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
    
    def get_queryset(self):
        return SubCategoria.objects.filter(categoria__usuario=self.request.user)

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
    
    
    
class CatalogoView(LoginRequiredMixin, generic.ListView):
    model = Producto
    template_name = "catalogos/catalogo.html"
    context_object_name = "obj"
    login_url='generales:login'

    def get_queryset(self):
        return Producto.objects.filter(usuario=self.request.user)
    
    

class MovimientosMercanciaView(SuccessMessageMixin, LoginRequiredMixin, SinPrivilegios, generic.CreateView):
    permission_required = 'movimientos.add_movimientos'
    model = Movimientos
    login_url = 'generales:login'
    template_name = 'catalogos/movimientosform.html'
    form_class = MovimientosEncForm
    success_url = reverse_lazy('generales:home')

    def get(self, request, *args, **kwargs):

        ctx = {'fecha': datetime.today(), 'tipo': kwargs["tipoe"], 'tercero': 0, 'ubicacion': 1, 'tipo_movimiento': 1}

        self.object = None

        form = MovimientosEncForm(initial=ctx)

        detalle_movimientos_formset = DetalleMovimientosFormSet()

        return self.render_to_response( 
            self.get_context_data(
                form=form,
                tipo=kwargs["tipoe"],
                opc_tipo_movimiento= Tipos_movimientos.objects.filter(tipo=kwargs["tipoe"],usuario=self.request.user),
                detalle_movimientos=detalle_movimientos_formset            
            )
        )

    def post(self, request, *args, **kwargs):
        form = MovimientosEncForm(request.POST)
        detalle_movimientos = DetalleMovimientosFormSet(request.POST)
        tipor = kwargs["tipoe"]
        #print("SSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSS")
        #print(form.errors)
        #print(detalle_movimientos.errors)
        if form.is_valid() and detalle_movimientos.is_valid():
            return self.form_valid(form, detalle_movimientos, tipor)
        else:
            return self.form_invalid(form, detalle_movimientos, tipor)

    def form_valid(self, form, detalle_movimientos, tipor):
        self.object = form.save(commit=False)
        profile = Profile.objects.filter(user=self.request.user.id).get()
        if tipor == 1:
            self.object.documento_no = profile.entrada + 1
            profile.entrada = profile.entrada + 1
        else:
            self.object.documento_no = profile.salida + 1
            profile.salida = profile.salida + 1
        profile.save()
        self.object.usuario = self.request.user
        self.object = form.save()
        detalle_movimientos.instance = self.object
        detalle_movimientos.save()
        for detalle in detalle_movimientos:
            if detalle.cleaned_data["cantidad"] is not None:
                producto=Producto.objects.filter(codigo_de_barra=detalle.cleaned_data["codigo_de_barra"], usuario=self.request.user.id).get()
                if tipor == 1:
                    producto.existencia = producto.existencia + detalle.cleaned_data["cantidad"]
                    if producto.costo_unidad != int(detalle.cleaned_data["costo"]):
                        producto.costo_unidad = (int(detalle.cleaned_data["costo"]) + producto.costo_unidad) / 2
                else:
                    producto.existencia = producto.existencia - detalle.cleaned_data["cantidad"]
                producto.save()
        
        
        return HttpResponseRedirect(reverse_lazy("catalogos:menu_inv"))
        #return JsonResponse(
        #    {
        #        'content': {
        #            'message': 'Generado Documento No. '+str(self.object.id)+' exitosamente.'
        #        }
        #    }
        #)


    def form_invalid(self, form, detalle_movimientos, tipor):
        self.object=form
        return self.render_to_response(
            self.get_context_data(
                form=form,
                detalle_movimientos=detalle_movimientos
            )
        )




class ProductoNew(SuccessMessageMixin, LoginRequiredMixin, SinPrivilegios, generic.CreateView):
    permission_required='catalogos.add_producto'
    model = Producto
    template_name="catalogos/productos_form.html"
    context_object_name = 'obj'
    form_class = ProductoForm
    success_message='Producto creado satisfactoriamente'

    def get(self, request, *args, **kwargs):
        self.object = None
        form_class = self.get_form_class()
        form = self.get_form(form_class)

        return self.render_to_response(
            self.get_context_data(
                form=form,
                retorna=kwargs["pk"]
            )
        )

    def post(self, request, *args, **kwargs):
        form_class = self.get_form_class()
        form = self.get_form(form_class)
        #print("XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX")
        #print(form.errors)
        if form.is_valid():
            return self.form_valid(form,kwargs["pk"])
        else:
            return self.form_invalid(form)

    def form_valid(self, form, pk):
        self.object = form.save(commit=False)
        self.object.usuario = self.request.user
        self.object = form.save()
        if pk == 1:
            return HttpResponseRedirect(reverse_lazy("catalogos:productos_list"))
        else:
            return HttpResponseRedirect(reverse_lazy("catalogos:catalogo"))

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
        terceros = SubCategoria.objects.filter(nombre__icontains=query, categoria__usuario=request.user).values("id","nombre") 
        terceros = list(terceros)
        return JsonResponse(terceros, safe=False) 
    else: 
        return JsonResponse(data={'success': False, 'errors': 'No encuentro resultados.'}) 


def get_ajaxTerceros(request, *args, **kwargs): 
    query = request.GET.get('q', None)
    if query: 
        terceros = Terceros.objects.filter(rzn_social__icontains=query, user=request.user).values("id","rzn_social") 
        terceros = list(terceros)
        return JsonResponse(terceros, safe=False) 
    else: 
        return JsonResponse(data={'success': False, 'errors': 'No encuentro resultados.'}) 



def get_ajaxBarcode(request, *args, **kwargs): 
    bar_code = request.GET.get('bar_code', None)
    if not bar_code:
        return JsonResponse(data={'nombre': '', 'errors': 'No encuentro producto.'})
    else:
        bar_code = Producto.objects.filter(codigo_de_barra=bar_code, usuario=request.user).last()
        if bar_code:
            return JsonResponse(data={"nombre": bar_code.nombre, "costo_unidad": bar_code.costo_unidad}, safe=False)
        else: 
            return JsonResponse(data={'nombre': '', 'errors': 'No encuentro producto.'})




def get_ajaxProducto(request, *args, **kwargs): 
    query = request.GET.get('q', None)
    if query: 
        terceros = Producto.objects.filter(nombre__icontains=query, usuario=request.user).values("id","nombre") 
        terceros = list(terceros)
        return JsonResponse(terceros, safe=False) 
    else: 
        return JsonResponse(data={'success': False, 'errors': 'No encuentro resultados.'})
    
    

def get_additem(request, *args, **kwargs):
    detalle_movimientos_formset = DetalleMovimientosFormSet()
    detalle_movimientos_formset.extra += 1
    return JsonResponse(data={'detalle_movimientos':detalle_movimientos_formset})


def get_ajaxCantidad(request, *args, **kwargs): 
    cantidad = request.GET.get('cantidad', None)
    bar_code = request.GET.get('producto', None)
    tipo = int(request.GET.get('tipo', None))
    if not cantidad:
        return JsonResponse(data={'existencia': '', 'errors': 'No encuentro producto.'})
    else:
        if tipo > 1:
            existencia = Producto.objects.filter(codigo_de_barra=bar_code, usuario=request.user).last()
            if bar_code:
                if int(cantidad) > existencia.existencia:
                    return JsonResponse(data={"errors": "CANTIDAD NO PUEDE SER SUPERIOR A LA EXISTENCIA DEL PRODUCTO"}, safe=False)
                else:
                    return JsonResponse(data={"errors": ""}, safe=False)
            else: 
                return JsonResponse(data={'errors': 'No encuentro producto.'})
        else:
            return JsonResponse(data={"errors": ""}, safe=False)
        
        
        
class FormulacionView(SuccessMessageMixin, LoginRequiredMixin, SinPrivilegios, generic.CreateView):
    permission_required = 'formulacion.add_formulacion'
    model = Formulacion
    login_url = 'generales:login'
    template_name = 'catalogos/formulacionform.html'
    form_class = FormulacionEncForm
    success_url = reverse_lazy('generales:home')

    def get(self, request, *args, **kwargs):

        self.object = None

        form = FormulacionEncForm()

        detalle_movimientos_formset = DetalleFormulacionFormSet()

        return self.render_to_response( 
            self.get_context_data(
                form=form,
                detalle_movimientos=detalle_movimientos_formset            
            )
        )

    def post(self, request, *args, **kwargs):
        
        form = FormulacionEncForm(request.POST)
        detalle_movimientos = DetalleFormulacionFormSet(request.POST)
        #print("SSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSS")
        #print(form.errors)
        #print(detalle_movimientos.errors)
        if form.is_valid() and detalle_movimientos.is_valid():
            return self.form_valid(form, detalle_movimientos)
        else:
            return self.form_invalid(form, detalle_movimientos)

    def form_valid(self, form, detalle_movimientos):
        self.object = form.save(commit=False)
        self.object.usuario = self.request.user
        self.object = form.save()
        detalle_movimientos.instance = self.object
        detalle_movimientos.save()

        return HttpResponseRedirect(reverse_lazy("generales:home"))

    def form_invalid(self, form, detalle_movimientos):
        self.object=form
        return self.render_to_response(
            self.get_context_data(
                form=form,
                detalle_movimientos=detalle_movimientos
            )
        )

def printer(self):
    profile = Profile.objects.filter(user=self.request.user.id).get()
    c = Conector()
    #c.texto("Imagen local:\n")
    # Recuerda que la imagen debe existir y debe ser legible para el plugin. Si no, comenta las líneas
    #c.imagenLocal(profile.logo)
    c.establecerJustificacion(AlineacionCentro)
    c.establecerTamanioFuente(2, 2)
    c.texto(profile.empresa+"\n")
    c.establecerTamanioFuente(1, 0)
    c.textoConAcentos(profile.nit+"\n")
    c.establecerEnfatizado(1)
    c.texto("titulo"+"\n")
    c.establecerEnfatizado(0)
    c.texto("Sin enfatizado\n")
    c.establecerTamanioFuente(2, 2)
    c.texto("Texto de 2, 2\n")
    c.establecerTamanioFuente(1, 1)
    c.establecerJustificacion(AlineacionCentro)
    c.texto("Texto centrado\n")
    c.texto("Código de barras:\n")
    c.codigoDeBarras("7506129445966", AccionBarcodeJan13)
    c.qrComoImagen("Parzibyte")
    c.texto("Imagen de URL:\n")
    #c.imagenDesdeUrl("https://github.com/parzibyte.png")
    c.texto("Imagen local:\n")
    # Recuerda que la imagen debe existir y debe ser legible para el plugin. Si no, comenta las líneas
    #c.imagenLocal(
    #    "C:\\Users\\Luis Cabrera Benito\\Desktop\\cliente_python_impresoras_termicas\\python-logo.png")
    c.feed(5)
    c.cortar()
    c.abrirCajon()
    print("Imprimiendo...")
    # Recuerda cambiar por el nombre de tu impresora
    respuesta = c.imprimirEn("POS-90")
    if respuesta == True:
        return JsonResponse(data={"errors": ""}, safe=False)
    else:
        return JsonResponse(data={"errors": "Error. El mensaje es: {respuesta}"}, safe=False)
