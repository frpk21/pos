from django.db import models
from django.template.defaultfilters import slugify
from decimal import Decimal
from django.contrib.auth.models import User
from generales.models import ClaseModelo
from catalogos.models import Producto
from django.dispatch import receiver
from django.db.models.signals import post_save, post_delete, pre_delete
from django.db.models import Sum


class Facturas(ClaseModelo):
    fecha_factura = models.DateField()
    factura = models.IntegerField(default=0)
    observacion = models.CharField(max_length=200, null=True,blank=True)
    valor_factura = models.DecimalField(max_digits=15, decimal_places=2, default=0)
    porc_iva = models.DecimalField(max_digits=5, decimal_places=2, default=0)
    valor_iva = models.DecimalField(max_digits=15, decimal_places=2, default=0)
    reteica = models.DecimalField(max_digits=15, decimal_places=2, default=0)
    reteiva = models.DecimalField(max_digits=15, decimal_places=2, default=0)
    retfuente = models.DecimalField(max_digits=15, decimal_places=2, default=0)
    saldo = models.DecimalField(max_digits=15, decimal_places=2, default=0)
    anulado = models.BooleanField(default=False, blank=True, null=True)
    usuario = models.ForeignKey(User, blank=False, null=False, on_delete=models.DO_NOTHING)
    recibido = models.DecimalField(max_digits=15, decimal_places=2, default=0)
    cambio = models.DecimalField(max_digits=15, decimal_places=2, default=0)
    efectivo = models.DecimalField(max_digits=15, decimal_places=2, default=0)
    tdebito = models.DecimalField(max_digits=15, decimal_places=2, default=0)
    tcredito = models.DecimalField(max_digits=15, decimal_places=2, default=0)
    transferencia = models.DecimalField(max_digits=15, decimal_places=2, default=0)
    bonos = models.DecimalField(max_digits=15, decimal_places=2, default=0)
    vales = models.DecimalField(max_digits=15, decimal_places=2, default=0)
    cerrado = models.BooleanField(default=False, blank=True, null=True)
    con_electronica = models.BooleanField(default=False, blank=True, null=True)
    descuento = models.DecimalField(max_digits=15, decimal_places=2, default=0)

    def __str__(self):
        return '{}'.format(self.fecha_factura)

    class Meta:
        verbose_name_plural="Encabezados de Facturas"
        verbose_name="Encabezado de Factura"


class Factp(ClaseModelo):
    factura=models.ForeignKey(Facturas, on_delete=models.CASCADE)
    prod=models.ForeignKey(Producto, on_delete=models.CASCADE)
    producto = models.CharField(max_length=100, help_text='Nombre del producto', blank=True, null=True, default='')
    codigo_de_barra = models.CharField(max_length=100, help_text='Código de Barra', blank=True, null=True, default='')
    cantidad=models.IntegerField(default=0)
    porc_iva = models.DecimalField(max_digits=5, decimal_places=2, default=0)
    valor_iva = models.DecimalField(max_digits=15, decimal_places=2, default=0)
    valor_unidad = models.DecimalField(max_digits=15, decimal_places=2, default=0)
    descuento = models.DecimalField(max_digits=15, decimal_places=2, default=0)
    valor_total = models.DecimalField(max_digits=15, decimal_places=2, default=0)

    def __str__(self):
        return '{}-{}'.format(self.factura, self.producto)

    def save(self):
        super(Factp,self).save()

    class Meta:
        verbose_name_plural="Detalles de Facturas"
        verbose_name="Detalle de Factura"
        
        
class FormasPagos(ClaseModelo):
    nombre = models.CharField(max_length=100, help_text='Nombre Forma de Pago')
    slug = models.SlugField(blank=True,null=True, max_length=250)
    

    def __str__(self):
        return '{}'.format(self.nombre)

    def save(self):
        self.nombre = self.nombre.upper()
        self.slug = slugify(self.nombre)
        super(FormasPagos,self).save()

    class Meta:
        verbose_name_plural="Nombre Forma de Pago"
        verbose_name="Nombres Formas de Pago"
             
class Cierres(ClaseModelo):
    fecha = models.DateTimeField()
    cierre_no = models.IntegerField(default=0, blank=True, null=True)
    valor_total_registrado = models.DecimalField(max_digits=15, decimal_places=2, default=0)
    base_caja = models.DecimalField(max_digits=15, decimal_places=2, default=0)
    usuario = models.ForeignKey(User, blank=False, null=False, on_delete=models.DO_NOTHING)
    pos_no = models.IntegerField(default=1)
    factura_desde = models.CharField(max_length=15)
    factura_hasta = models.CharField(max_length=15)

    def __str__(self):
        return '{}-{}'.format(self.id, self.valor_total_registrado)

    def save(self, *args, **kwargs):
        super(Cierres, self).save(*args, **kwargs)

    class Meta:
        verbose_name_plural="Cierre de Caja"
        verbose_name="Cierres de Cajas"

class Cierres1(ClaseModelo):
    cierre = models.ForeignKey(Cierres, blank=False, null=False, on_delete=models.CASCADE)
    ventas_descuentos = models.DecimalField(max_digits=15, decimal_places=2, default=0)
    ventas_cubcat = models.DecimalField(max_digits=15, decimal_places=2, default=0)
    ventas_cubcat_excentas = models.DecimalField(max_digits=15, decimal_places=2, default=0)
    ventas_cubcat_excluidas = models.DecimalField(max_digits=15, decimal_places=2, default=0)
    ventas_cubcat_grabadas = models.DecimalField(max_digits=15, decimal_places=2, default=0)

    def __str__(self):
        return '{}'.format(self.ventas_subcat)

    def save(self, *args, **kwargs):
        super(Cierres1,self).save(*args, **kwargs)

    class Meta:
        verbose_name_plural="Detalle cierre de caja."
        verbose_name="DEtalles cierres de caja."

class GrabadosCierres1(ClaseModelo):
    cierre = models.ForeignKey(Cierres, blank=False, null=False, on_delete=models.CASCADE)
    tarifa_iva =  models.DecimalField(max_digits=6, decimal_places=2, default=0)
    base_iva =  models.DecimalField(max_digits=15, decimal_places=2, default=0)
    valor_iva =  models.DecimalField(max_digits=15, decimal_places=2, default=0)

    def __str__(self):
        return '{}-{}'.format(self.tarifa_iva, self.valor_iva)

    def save(self, *args, **kwargs):
        super(GrabadosCierres1,self).save(*args, **kwargs)

    class Meta:
        verbose_name_plural="IVA grabado"
        verbose_name="IVA grabados"

class FormasPagosCierres1(ClaseModelo):
    cierre = models.ForeignKey(Cierres, blank=False, null=False, on_delete=models.CASCADE)
    forma_pago = models.CharField(max_length=100, help_text='Forma de Pago')
    valor_pago =  models.DecimalField(max_digits=15, decimal_places=2, default=0)

    def __str__(self):
        return '{}-{}'.format(self.forma_pago, self.valor_pago)

    def save(self, *args, **kwargs):
        super(FormasPagosCierres1,self).save(*args, **kwargs)

    class Meta:
        verbose_name_plural="Froma de Pago"
        verbose_name="Formas de Pagos"


@receiver(post_save, sender=Cierres)
def crear_cierres1(sender, instance, created, **kwargs):
    cierre_creado = instance
    ventas = Factp.objects.filter(factura__cerrado=False, factura__usuario=cierre_creado.usuario)
    result0 = Factp.objects.values('porc_iva').order_by('porc_iva').filter(factura__cerrado=False, factura__usuario=cierre_creado.usuario).annotate(tot_iva=Sum('valor_iva'),base=Sum('valor_unidad'))
    if created:
        efectivo = Facturas.objects.values('efectivo').order_by('efectivo').filter(cerrado=False, usuario=cierre_creado.usuario, efectivo__gte = 1).aggregate(total=Sum('efectivo'))
        tdebito = Facturas.objects.values('tdebito').order_by('tdebito').filter(cerrado=False, usuario=cierre_creado.usuario).aggregate(total=Sum('tdebito'))
        tcredito = Facturas.objects.values('tcredito').order_by('tcredito').filter(cerrado=False, usuario=cierre_creado.usuario).aggregate(total=Sum('tcredito'))
        transferencias = Facturas.objects.values('transferencia').order_by('transferencia').filter(cerrado=False, usuario=cierre_creado.usuario).aggregate(total=Sum('transferencia'))
        bonos = Facturas.objects.values('bonos').order_by('bonos').filter(cerrado=False, usuario=cierre_creado.usuario).aggregate(total=Sum('bonos'))
        vales = Facturas.objects.values('vales').order_by('vales').filter(cerrado=False, usuario=cierre_creado.usuario).aggregate(total=Sum('vales'))
        descuentos,excentos,excluidos,grabados,tventas = 0,0,0,0,0
        for i, item in enumerate(ventas):
            if item.descuento > 0:
                descuentos += item.descuento
            if item.valor_iva < 1:
                excentos += (item.valor_unidad * item.cantidad)
                excluidos += (item.valor_unidad * item.cantidad)
            else:
                grabados += (item.valor_unidad * item.cantidad)
       # res = Factp.objects.values('prod__subcategoria__nombre').order_by('prod__subcategoria').filter(factura__cerrado=False, factura__usuario=cierre_creado.usuario).annotate(total1=Sum('valor_unidad'))
        FormasPagosCierres1.objects.get_or_create(
            cierre=cierre_creado,
            forma_pago="EFECTIVO",
            valor_pago=efectivo["total"]
        )
        FormasPagosCierres1.objects.get_or_create(
            cierre=cierre_creado,
            forma_pago="TARJETAS DEBITO",
            valor_pago=tdebito["total"]
            )
        FormasPagosCierres1.objects.get_or_create(
            cierre=cierre_creado,
            forma_pago="TARJETAS CREDITO",
            valor_pago=tcredito["total"]
            )
        FormasPagosCierres1.objects.get_or_create(
            cierre=cierre_creado,
            forma_pago="TRANSFERENCIAS BANCARIAS",
            valor_pago=transferencias["total"]
            )
        FormasPagosCierres1.objects.get_or_create(
            cierre=cierre_creado,
            forma_pago="BONOS",
            valor_pago=bonos["total"]
            )  
        FormasPagosCierres1.objects.get_or_create(
            cierre=cierre_creado,
            forma_pago="VALES",
            valor_pago=vales["total"]
            ) 
        Cierres1.objects.get_or_create(
            cierre=cierre_creado,
            ventas_descuentos=descuentos,
            ventas_cubcat_excentas=excentos,
            ventas_cubcat_excluidas=excluidos,
            ventas_cubcat_grabadas=grabados
            )
        for i, item in enumerate(result0):
            if item["porc_iva"] > 0:
                GrabadosCierres1.objects.get_or_create(
                    cierre = cierre_creado,
                    tarifa_iva =  item["porc_iva"],
                    base_iva =  item["base"],
                    valor_iva =  item["tot_iva"]
            )



class HookDian(ClaseModelo):
    factura = models.ForeignKey(Facturas, on_delete=models.CASCADE)
    tascode =  models.CharField('TasCode', max_length=200, blank=True, null=True)
    fecha =  models.DateTimeField(auto_now_add=True)
    text = models.CharField('Text', max_length=2000, blank=True, null=True)
    alert = models.CharField('Alert', max_length=1000, blank=True, null=True)
    process = models.CharField('Process', max_length=1000, blank=True, null=True)
    process_change = models.BooleanField(default=False, blank=True, null=True)
    CHOICES = ((0,'En Cola'),(1,'En Proceso DIAN'),(2,'Aceptado DIAN'),(90,'Rechazado DIAN'))
    process_newvalue = models.IntegerField(choices=CHOICES, default=0, blank=True, null=True)
    email = models.CharField('eMail', max_length=1000, blank=True, null=True)
    email_evento = models.CharField('eMail del evento', max_length=1000, blank=True, null=True)
    CHOICES1 = ((0,'Correo Enviado'),(1,'Correo Recibido'),(2,'Correo Rechazado'),(98,'En Lista Negra'))
    email_status = models.IntegerField(choices=CHOICES1, default=0, blank=True, null=True)
    usuario = models.ForeignKey(User, blank=False, null=False, on_delete=models.DO_NOTHING)
    
    def __str__(self):
        return '{}'.format(self.factura)

    
    def save(self, force_insert=False, force_update=False, using=None, update_fields=None):
        super(HookDian,self).save(force_insert, force_update, using, update_fields)

    class Meta:
        verbose_name_plural="Respuestas API Facturas"
        verbose_name="Respuesta APP Factura"





class FactDian(ClaseModelo):
    factura = models.ForeignKey(Facturas, on_delete=models.CASCADE)
    modes = models.CharField('Mode', max_length=20, blank=True, null=True)
    tascode = models.CharField('Tascode', max_length=500, blank=True, null=True)
    intid = models.CharField('IntID', max_length=20, blank=True, null=True)
    process = models.CharField('Process', max_length=3, blank=True, null=True)
    retries = models.CharField('retries', max_length=2, blank=True, null=True)
    cufe = models.CharField('Cufe', max_length=500, blank=True, null=True)
    url = models.URLField('url para decarga el documento ', blank=True, null=True, max_length=1000, default="")
    pdf = models.URLField('url para decarga del pdf', blank=True, null=True, max_length=1000, default="")
    attached = models.URLField('url para decarga del xml del documento enviado al cliente', blank=True, null=True, max_length=1000, default="")
    pdf_file = models.FileField(upload_to="facturas/pdf", blank=True, null=True, default='')
    xlm_file = models.FileField(upload_to="facturas/xml", blank=True, null=True, default='')
    usuario = models.ForeignKey(User, blank=False, null=False, on_delete=models.DO_NOTHING)
    
    def __str__(self):
        return '{}-{}'.format(self.factura, self.customer)

    
    def save(self, force_insert=False, force_update=False, using=None, update_fields=None):
        super(FactDian,self).save(force_insert, force_update, using, update_fields)

    class Meta:
        verbose_name_plural="Documentos DIAN Facturas"
        verbose_name="Documento DIAN Factura"


class NotasCredito(ClaseModelo):
    no_nota = models.CharField('Numero de Documento', blank=False, null=False, max_length=40, default="", primary_key=True)
    usuario = models.ForeignKey(User, blank=False, null=False, on_delete=models.DO_NOTHING)
    fecha = models.DateField(auto_now_add=True)
    factura=models.ForeignKey(FactDian, on_delete=models.DO_NOTHING, default='', null=False, blank=False)
    rangekey = models.CharField('rangeKey', max_length=100, blank=True, null=True)
    issuedate = models.DateField()
    issuetime = models.TimeField()
    CHOICES = (
        (1,'Devolucion parcial de bienes y/o no aceptacion parcial del servicio'),
        (2,'Anulacion de factura electronica'),
        (3,'Rebaja o descuento parcial o total'), 
        (4,'Ajuste de precio'),
        (5,'Otros')
        )
    discrepancycode = models.IntegerField(choices=CHOICES, default=1, blank=False, null=False)
    note1 = models.CharField('Note1', max_length=1000, blank=True, null=True)
    note2 = models.CharField('Note2', max_length=200, blank=True, null=True)
    note3 = models.CharField('Note3', max_length=500, blank=True, null=True)
    note4 = models.CharField('Note4', max_length=500, blank=True, null=True)
    CHOICES1 = (
        (0,'Enlace de descarga (Por omision)'),
        (1,'Como archivos adjuntos'),
        (2,'Archivos adjuntos y Asunto homologado DIAN'), 
        (99,'No se envia correo')
        )
    emailstyle = models.IntegerField(choices=CHOICES1, default=1, blank=False, null=False)
    totalamount = models.DecimalField('Valor Total Nota', max_digits=15, decimal_places=2, default=0)
    discountamount = models.DecimalField('Total descuentos Realizados', max_digits=15, decimal_places=2, default=0)
    extraamount = models.DecimalField('Total de Cargos Adicionales Cobrados', max_digits=15, decimal_places=2, default=0)
    taxamount = models.DecimalField('Total de Impuestos Cobrados', max_digits=15, decimal_places=2, default=0)
    prepaidamount = models.DecimalField('Total de Pagos Anticipados', max_digits=15, decimal_places=2, default=0)
    payamount = models.DecimalField('Total a Pagar', max_digits=15, decimal_places=2, default=0)
    whTaxAmount = models.DecimalField('Total de Retenciones Aplicadas (whTaxes)', max_digits=15, decimal_places=2, default=0)
    anulado = models.BooleanField(default=False, blank=True, null=True)

    def __str__(self):
        return '{}-{}'.format(self.no_nota, self.factura)

    class Meta:
        verbose_name_plural="Notas Credito"
        verbose_name="Nota credito"

    def save(self):
        super(NotasCredito, self).save()


class NotasCredito1(ClaseModelo):
    no_nota = models.ForeignKey(NotasCredito, on_delete=models.CASCADE)
    cantidad=models.DecimalField(max_digits=15, decimal_places=2, default=0)
    valor_unidad=models.DecimalField(max_digits=15, decimal_places=2, default=0)
    total=models.DecimalField(max_digits=15, decimal_places=2, default=0)
    precio_referencia=models.DecimalField(max_digits=15, decimal_places=2, default=0)
    descripcion=models.CharField(max_length=100, blank=True, null=True)
    marca=models.CharField(max_length=100, blank=True, null=True)
    modelo=models.CharField(max_length=100, blank=True, null=True)
    standard=models.CharField(max_length=100, blank=True, null=True)
    agencia=models.CharField(max_length=100, blank=True, null=True)
    codigo_producto=models.CharField(max_length=100, blank=True, null=True)
    cargos_o_descuentos=models.BooleanField(default=False, blank=True, null=True)
    CHOICES = (
        ('00','Descuento por impuesto asumido'),
        ('01','Pague uno lleve otro'),
        ('02','Descuentos contractuales'),
        ('03','Descuento por pronto pago'), 
        ('04','Envío gratis'),
        ('05','Descuentos especificos por inventarios'),
        ('06','Descuento por monto de compras'),
        ('07','Descuento de temporada'),
        ('08','Descuento por acturalizacion de productos / servicios'),
        ('09','Descuento general'),
        ('10','Descuento por volumen'),
        ('11','Otro descuento')
        )
    codigo_dian_cargo_descuento=models.CharField(choices=CHOICES, max_length=2, blank=True, null=True)
    descripcion_cargo_descuento=models.CharField(max_length=100, blank=True, null=True)
    base_cargo_descuento=models.DecimalField(max_digits=15, decimal_places=2, default=0)
    valor_cargo_descuento=models.DecimalField(max_digits=15, decimal_places=2, default=0)
    porcentaje_cargo_descuento=models.DecimalField(max_digits=8, decimal_places=2, default=0)
    CHOICES = (
        ('01','IVA'),
        ('02','ICA'),
        ('03','Consumo'), 
        ('04','Bolsas'),
        ('22','Descuentos especificos por inventarios')
        )
    id_tax=models.CharField(choices=CHOICES, max_length=2, blank=True, null=True)
    valor_total_tax=models.DecimalField(max_digits=15, decimal_places=2, default=0)
    porcentaje_tax=models.DecimalField(max_digits=8, decimal_places=2, default=0)
    valor_unidad_tax=models.DecimalField(max_digits=15, decimal_places=2, default=0)

    def __str__(self):
        return '{}-{}'.format(self.no_nota, self.descripcion)

    class Meta:
        verbose_name_plural="Notas Credito Detalles"
        verbose_name="Nota Credito Detalle"

    def save(self):
        super(NotasCredito1, self).save()



class Vales(ClaseModelo):
    vale_no = models.IntegerField(default=0)
    fecha = models.DateTimeField()
    usuario = models.ForeignKey(User, blank=False, null=False, on_delete=models.DO_NOTHING)
    concepto = models.CharField(max_length=200, null=True,blank=True)
    valor = models.DecimalField(max_digits=15, decimal_places=2, default=0)
    beneficiario = models.CharField(max_length=100, help_text='Nombre Beneficiario')
    anulado = models.BooleanField(default=False, blank=True, null=True)

    def __str__(self):
        return '{}'.format(self.beneficiario)

    def save(self):
        self.beneficiario = self.beneficiario.upper()
        self.concepto = self.concepto.upper()
        super(Vales,self).save()
        
    class Meta:
        verbose_name_plural="Vale de Caja"
        verbose_name="Vales de Caja"