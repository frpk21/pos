from django.urls import include, path

from catalogos.views import CategoriaView, CategoriaNew, CategoriaEdit, CategoriaDel,\
    SubCategoriaView, SubCategoriaNew, SubCategoriaEdit, SubCategoriaDel, ProductoView,\
    ProductoNew, ProductoEdit, ProductoDel

from catalogos.views import verMysql

urlpatterns = [
    path('categorias', CategoriaView.as_view(), name='categoria_list'),
    path('categorias/new', CategoriaNew.as_view(), name='categoria_new'),
    path('categorias/edit/<int:pk>', CategoriaEdit.as_view(), name='categoria_edit'),
    path('categorias/delete/<int:pk>', CategoriaDel.as_view(), name='categoria_delete'),
    path('facturas', verMysql, name='verMysql_list'),
    path('subcategorias', SubCategoriaView.as_view(), name='subcategoria_list'),
    path('subcategorias/new', SubCategoriaNew.as_view(), name='subcategoria_new'),
    path('subcategorias/edit/<int:pk>', SubCategoriaEdit.as_view(), name='subcategoria_edit'),
    path('subcategorias/delete/<int:pk>', SubCategoriaDel.as_view(), name='subcategoria_delete'),
    path('productos', ProductoView.as_view(), name='productos_list'),
    path('productos/new', ProductoNew.as_view(), name='producto_new'),
    path('productos/edit/<int:pk>', ProductoEdit.as_view(), name='producto_edit'),
    path('productos/delete/<int:pk>', ProductoDel.as_view(), name='producto_delete'),
]