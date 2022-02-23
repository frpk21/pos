from django.urls import include, path
from catalogos import views
from catalogos.views import CategoriaView, CategoriaNew, CategoriaEdit, CategoriaDel,\
    SubCategoriaView, SubCategoriaNew, SubCategoriaEdit, SubCategoriaDel, ProductoView,\
    ProductoNew, ProductoEdit, ProductoDel, CatalogoView, MovimientosMercanciaView, get_additem


urlpatterns = [
    path('mainprod', views.MenuView, name='menu'),
    path('maininv', views.MenuInvView, name='menu_inv'),
    path('categorias', CategoriaView.as_view(), name='categoria_list'),
    path('categorias/new', CategoriaNew.as_view(), name='categoria_new'),
    path('categorias/edit/<int:pk>', CategoriaEdit.as_view(), name='categoria_edit'),
    path('categorias/delete/<int:pk>', CategoriaDel.as_view(), name='categoria_delete'),
    path('subcategorias', SubCategoriaView.as_view(), name='subcategoria_list'),
    path('subcategorias/new', SubCategoriaNew.as_view(), name='subcategoria_new'),
    path('subcategorias/edit/<int:pk>', SubCategoriaEdit.as_view(), name='subcategoria_edit'),
    path('subcategorias/delete/<int:pk>', SubCategoriaDel.as_view(), name='subcategoria_delete'),
    path('inv/move/<int:tipoe>', views.MovimientosMercanciaView.as_view(), name='movimientos'),
    path('inv/frm/new', views.FormulacionView.as_view(), name='formulacion'),
   # path('inv/mov/add', get_additem, name='add_item'),
    path('barcode/', views.get_ajaxBarcode, name='valida_bar_code'),
   # path('repo1/', views.get_ajaxInformeMovimientos1, name='info_movimientos1'),
    path('repo1/res/', views.InformeMovimientos1View.as_view(), name='info_movimientos1_resul'),
    path('repo1/res/<int:pk>/<int:tipo>/<int:documento_no>', views.imprimirMovimiento, name='imp_movimiento'),
    
    path('cantidad/', views.get_ajaxCantidad, name='valida_cantidad'),
    path('productos', ProductoView.as_view(), name='productos_list'),
    path('catalogo', CatalogoView.as_view(), name='catalogo'),
    path('catalogo/print/pdf', views.imprimirCatalogo, name='imp_catalogo'),
    path('catalogo/print/xls', views.imprimirCatalogoXls, name='imp_catalogo_xls'),
    path('catalogo/cod/<pk>', views.BarCodePrint, name='imp_barcode'),
    path('productos/new/<int:pk>', ProductoNew.as_view(), name='producto_new'),
    path('productos/edit/<int:pk>', ProductoEdit.as_view(), name='producto_edit'),
    path('productos/delete/<int:pk>', ProductoDel.as_view(), name='producto_delete'),
    path('subcategoria/', views.get_ajaxSubcategoria, name='subcategoria_select2'),
    path('search/producto/', views.get_ajaxProducto, name='producto_select2'),
    path('terceros/', views.get_ajaxTerceros, name='terceros_select2'),
    path('tarifas/iva', views.TarifasIvaListView.as_view(), name='tarifas_iva'),
    path('tarifas/iva/new', views.TarifaIvaNew.as_view(), name='tarifa_iva_new'),
    path('tarifas/iva/edit/<int:pk>', views.TarifaIvaEdit.as_view(), name='tarifa_iva_edit'),
    path('tarifas/iva/delete/<int:pk>', views.TarifaIvaDel.as_view(), name='tarifa_iva_delete'),
]