from django.db import models

from generales.models import ClaseModelo

from generales.models import Terceros, Ciudad

from django.contrib.auth.models import User

from datetime import datetime

# Create your models here.

class Categoria(ClaseModelo):
    usuario = models.ForeignKey(User, blank=True, null=True, on_delete=models.DO_NOTHING)
    nombre = models.CharField(
        max_length=100,
        help_text='Nombre de la categoría',
        unique=True,
    )

    def __str__(self):
        return '{}'.format(self.nombre)

    def save(self):
        self.nombre = self.nombre.upper()
        self.usuario = self.request.user
        super(Categoria, self).save()

    class Meta:
        verbose_name_plural = "Categorias"



class SubCategoria(ClaseModelo):
    categoria = models.ForeignKey(Categoria, on_delete=models.CASCADE)
    nombre = models.CharField(
        max_length=100,
        help_text='Descripción de la sub categoría'
    )

    def __str__(self):
        return '{}:{}'.format(self.categoria.nombre,self.nombre)

    def save(self):
        self.nombre = self.nombre.upper()
        super(SubCategoria, self).save()

    class Meta:
        verbose_name_plural = "Sub Categorias"
        unique_together = ('categoria','nombre')



class Ubicaciones(ClaseModelo):
    usuario = models.ForeignKey(User, blank=True, null=True, on_delete=models.DO_NOTHING)
    descripcion = models.CharField(max_length=100, help_text='Descripción de la Ubicación')
    ciudad = models.ForeignKey(Ciudad, on_delete=models.CASCADE, default=0, null=False, blank=False)
    direccion = models.CharField(max_length=100, help_text='Dirección')
    
    def __str__(self):
        return '{}:{}'.format(self.descripcion,self.ciudad)

    def save(self):
        self.descripcion = self.descripcion.upper()
        self.usuario = self.request.user
        super(Ubicaciones, self).save()

    class Meta:
        verbose_name_plural = "Ubicaciones"
        unique_together = ('direccion','descripcion')



class Iva(ClaseModelo):
    tarifa_iva = models.IntegerField(default=19, blank=False, null=False)
    
    def __str__(self):
        return '{}'.format(self.tarifa_iva)

    def save(self):
        super(Iva, self).save()

    class Meta:
        verbose_name_plural = "TARIFAS IVA"
        unique_together = ('tarifa_iva',)



class Producto(ClaseModelo):
    subcategoria = models.ForeignKey(SubCategoria, on_delete=models.CASCADE)
    nombre = models.CharField(max_length=100, help_text='Nombre del producto')
    descripcion = models.CharField(max_length=100, help_text='Descripción del producto')
    archivo_foto = models.FileField(upload_to="fotos/", blank=True, null=True, default='')
    CHOICES = ( (1,'UNIDAD'),(2,'KILOGRAMO'),(3,'GRAMO'),(4,'MILIGRAMO'),(5,'METRO'),(6,'CENTIMETRO'),(7,'MILIMETRO'),\
        (8,'LITRO'), (9,'MILILITRO'), (10,'CENTILITRO'), (11,'METRO CUADRADO'), (12,'CENTIMETRO CUADRADO'), (13,'LITRO') )
    unidad_de_medida = models.IntegerField(choices=CHOICES, default=1, blank=False, null=False)
    proveedor = models.ManyToManyField(Terceros, related_name='Proveedores_del_producto') 
    #models.ForeignKey(Terceros, models.DO_NOTHING, blank=True, null=True, default=0)
    existencia = models.DecimalField('EXISTENCIA', max_digits=12, decimal_places=2, default=0, blank=True, null=True)
    stock_minimo = models.DecimalField('STOCK MINIMO', max_digits=10, decimal_places=0, default=0, blank=True, null=True)
    stock_maximo = models.DecimalField('STOCK MAXIMO', max_digits=10, decimal_places=0, default=0, blank=True, null=True)
    total_copas = models.DecimalField('TOTAL COPAS', max_digits=10, decimal_places=0, default=0, blank=True, null=True)
    descuento_promo = models.DecimalField('DESCUENTO PROMOCIONAL', max_digits=2, decimal_places=0, default=0, blank=True, null=True)
    costo_unidad = models.DecimalField('VALOR COMPRA', max_digits=12, decimal_places=2, default=0, blank=True, null=True)
    tarifa_iva = models.ForeignKey(Iva, models.DO_NOTHING)
    precio_de_venta = models.DecimalField('PRECIO DE VENTA', max_digits=12, decimal_places=0, default=0, blank=True, null=True)
    fecha_de_vencimiento = models.DateTimeField('Fecha de Vencimiento', blank=True, null=True)
    codigo_de_barra = models.CharField(max_length=100, help_text='Código de Barra', blank=True, null=True, default='')
    ubicacion = models.ForeignKey(Ubicaciones, on_delete=models.DO_NOTHING, default=1, null=False, blank=False)
    cuenta_contable_ventas_locales = models.CharField('Cuenta de contabilidad para ventas locales', blank=True, null=True, max_length=20, default="")
    cuenta_contable_ventas_exterior = models.CharField('Cuenta de contabilidad para ventas al exterior', blank=True, null=True, max_length=20, default="")
    usuario = models.ForeignKey(User, blank=True, null=True, on_delete=models.DO_NOTHING)

    def __str__(self):
        return '{}'.format(self.nombre)

    def save(self):
        self.nombre = self.nombre.upper()
        super(Producto, self).save()

    class Meta:
        verbose_name_plural = "Productos"
        



class Tipos_movimientos(ClaseModelo):
    nombre = models.CharField(max_length=100, help_text='Nombre Tipo de Movimiento', unique=True)
    tipo = models.IntegerField(default=1, blank=False, null=False)

    def __str__(self):
        return '{}'.format(self.nombre)

    def save(self):
        self.nombre = self.nombre.upper()
        super(Tipos_movimientos, self).save()

    class Meta:
        verbose_name_plural = "Tipos de Movimientos"

      
class Movimientos(ClaseModelo):
    documento_no = models.IntegerField('CONSECUTIVO ENTRADAS DE ALMACEN', default=0, blank=False, null=False)
    usuario = models.ForeignKey(User, blank=True, null=True, on_delete=models.DO_NOTHING)
    fecha = models.DateTimeField('Fecha documento', blank=False, null=False)
    tipo =  models.ForeignKey(Tipos_movimientos, on_delete=models.CASCADE)
    tercero =  models.ForeignKey(Terceros, on_delete=models.CASCADE)
    ubicacion = models.ForeignKey(Ubicaciones, on_delete=models.DO_NOTHING, default=1, null=False, blank=False)
    valor_documento = models.DecimalField('Valor documento', max_digits=12, decimal_places=0, default=0, blank=True, null=True)

    def __str__(self):
        return '{}:{}'.format(self.tipo.nombre,self.id)

    def save(self):
        super(Movimientos, self).save()

    class Meta:
        verbose_name_plural = "Movimientos"
        
        
        
class Movimientos_detalle(ClaseModelo):
    movimiento = models.ForeignKey(Movimientos, on_delete=models.CASCADE)
    producto =  models.CharField(max_length=100, help_text='Nombre del producto', blank=True, null=True, default='')
    codigo_de_barra = models.CharField(max_length=100, help_text='Código de Barra', blank=True, null=True, default='')
    cantidad = models.DecimalField('Cantidad', max_digits=12, decimal_places=2, default=0, blank=True, null=True)
    costo = models.DecimalField('Precio de Compra', max_digits=12, decimal_places=0, default=0, blank=True, null=True)
    total = models.DecimalField('Total', max_digits=12, decimal_places=0, default=0, blank=True, null=True)

    def __str__(self):
        return '{}'.format(self.producto)

    def save(self):
        super(Movimientos_detalle, self).save()

    class Meta:
        verbose_name_plural = "Movimientos Detalles"


class Formulacion(ClaseModelo):
    
    producto = models.ForeignKey(Producto, on_delete=models.CASCADE)
    nombre = models.CharField(max_length=100, help_text='Nombre de Formula')
    usuario = models.ForeignKey(User, blank=True, null=True, on_delete=models.DO_NOTHING)
    

    def __str__(self):
        return '{}'.format(self.nombre)

    def save(self):
        self.nombre = self.nombre.upper()
        super(Formulacion, self).save()

    class Meta:
        verbose_name_plural = "Formulas"
        
        
class Formulacion1(ClaseModelo):
    
    formula = models.ForeignKey(Formulacion, on_delete=models.CASCADE)
    producto = models.ForeignKey(Producto, on_delete=models.CASCADE)
    cantidad = models.DecimalField('Cantidad', max_digits=12, decimal_places=2, default=0, blank=True, null=True)
    

    def __str__(self):
        return '{}'.format(self.formula)

    def save(self):
        super(Formulacion1, self).save()

    class Meta:
        verbose_name_plural = "Detalles Formula"