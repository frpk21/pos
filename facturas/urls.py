from django.urls import path
from facturas.views import FacturaList, FacturaNew, FacturaEdit
from facturas import views

urlpatterns = [
    path('Facturas/list', FacturaList.as_view(), name='factura_list'),
    path('Facturas/posnew', FacturaNew.as_view(), name="pos_new"),
    path('Facturas/posedit/<int:pk>', FacturaEdit.as_view(), name="factura_edit"),
    path('Facturas/menu', views.MenuView, name='menu'),
    path('barcode/', views.get_ajaxBarcode, name='valida_bar_code'),
    path('Factura/valter/', views.get_ajax_valida_nit, name='valida_nit'),
    path('Valclose/', views.get_ajax_valida_cierres, name='valida_cierres'),
    path('Facturas/posnew/prn/<factura>/<total>/<iva_pagado>/<neto>/<recibido>/<cambio>/<efectivo>/<tdebito>/<tcredito>/<transferencia>/<bonos>/<credito>/<descuento>', views.resul_pos, name='resul_pos'),
    path('Facturas/posclose', views.CierreCajaView.as_view(), name="pos_cierre"),
    path('Facturas/posclose/doing', views.CierreDoing, name="closed"),
    path('Facturas/posclose/print/<int:cierre>', views.imprimirCierre, name="imprimirCierre"),
    path('Vales/list', views.ValesList.as_view(), name="lista_vales"),
    path('Vales/new', views.ValesNew.as_view(), name="vales_new"),
    path('Vales/new/prn/<vale_no>/<fecha>/<beneficiario>/<concepto>/<valor>', views.resul_vales, name='resul_vales'),
    path('Cartera/list', views.EstadoCarteraList.as_view(), name='estado_cartera'),
    path('Pago/', views.get_ajax_pago_cartera, name='pago_cartera'),
    path('repo1/res/', views.InformePagosView.as_view(), name='info_pagos'),
    path('Indicadores', views.MenuIndicadoresView, name='menu_ind'),
    path('Facturas/info/', views.InformeVentasMesView.as_view(), name='info_ventas_mes'),
]