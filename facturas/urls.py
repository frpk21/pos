from django.urls import path
from facturas.views import FacturaList, FacturaNew, FacturaEdit

urlpatterns = [
    path('facturas', FacturaList.as_view(), name='factura_list'),
    path('factura/new', FacturaNew.as_view(), name="factura_new"),
    path('factura/edit/<int:pk>', FacturaEdit.as_view(), name="factura_edit")
]