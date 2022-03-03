from django.urls import path
from facturas.views import FacturaList, FacturaNew, FacturaEdit
from facturas import views

urlpatterns = [
    path('Facturas/list', FacturaList.as_view(), name='factura_list'),
    path('Facturas/posnew', FacturaNew.as_view(), name="pos_new"),
    path('Facturas/posedit/<int:pk>', FacturaEdit.as_view(), name="factura_edit"),
    path('Facturas/menu', views.MenuView, name='menu'),
    path('barcode/', views.get_ajaxBarcode, name='valida_bar_code'),
    path('Facturas/posnew/prn/<factura>/<total>/<recibido>/<cambio>/<efectivo>/<tdebito>/<tcredito>/<transferencia>/<bonos>', views.resul_pos, name='resul_pos'),
    path('Facturas/pos/print/<factura>', views.imprimirTiquete, name='print_pos'),
    path('Print/<factura>/<total>/<recibido>/<cambio>', views.imprimir, name='imprimir'),
]