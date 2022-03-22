from django.db import models
from ckeditor.fields import RichTextField
from django.contrib.auth.models import User

class ClaseModelo(models.Model):
    activo = models.BooleanField(default=True, null=True)
    creado = models.DateField(auto_now_add=True, null=True)
    modificado = models.DateField(auto_now=True, null=True)

    class Meta:
        abstract=True 


class Pais(ClaseModelo):
    nombre_pais = models.CharField('Nombre del Pais', blank=False, null=False, max_length=100, default="")

    class Meta:
        ordering = ['nombre_pais']
        verbose_name_plural = "Paises"

    def __str__(self):
        return '{}'.format(self.nombre_pais)

    def save(self):
        self.nombre_pais = self.nombre_pais.upper()
        super(Pais, self).save()


class Departamento(ClaseModelo):
    cod_dane = models.CharField('Codigo DANE del Departamento', blank=False, null=False, max_length=15, default="")
    nombre_departamento = models.CharField(blank=False, null=False, max_length=100, default="")
    pais = models.ForeignKey(Pais, on_delete=models.CASCADE, default=1, null=False, blank=False)

    def __str__(self):
        return '{}'.format(self.nombre_departamento)

    def save(self):
        self.nombre_departamento = self.nombre_departamento.upper()
        super(Departamento, self).save()

    class Meta:
        verbose_name_plural = "Departamentos"


class Ciudad(ClaseModelo):
    cod_dane = models.IntegerField('COD-DANE', default=0, blank=True, null=True)
    nombre_ciudad = models.CharField('Nombre del municipio/ciudad', blank=False, null=False, max_length=100, default="")
    #nombre = models.CharField('Nombre del municipio/ciudad', blank=False, null=False, max_length=100, default="")
    departamento = models.ForeignKey(Departamento, on_delete=models.CASCADE, default=0, null=False, blank=False)

    class Meta:
        ordering = ['nombre_ciudad',]
        verbose_name = 'Ciudad'
        verbose_name_plural = "Ciudades"

    def __str__(self):
        return '{}: {}'.format(self.nombre_ciudad, self.departamento.nombre_departamento)

    def save(self):
        self.nombre_ciudad = self.nombre_ciudad.upper()
        super(Ciudad, self).save()

class Terceros(ClaseModelo):
    nit = models.CharField('NIT/CC',blank=True, null=True, max_length=200, default="")
    CHOICES = ((11,'REGISTRO CIVIL'),(12,'TARJETA DE IDENTIDAD'),(13,'CEDULA DE CIUDADANIA'),(21,'TARJETA DE EXTRANJERIA'),(22,'CEDULA DE EXTRANJERIA'),(31,'NIT'),(41,'PASAPORTE'),(42,'TIPO DE DOCUMENTO DE EXTRANJERIA'))
    tipo_nit = models.IntegerField(choices=CHOICES, default=31, blank=True, null=True)
    dv_nit = models.CharField('Digito de Verificacion', default=0, blank=True, null=True, max_length=1)
    CHOICES1 = ((1,'REGIMEN SIMPLIFICADO'),(2,'REGIMEN COMUN'))
    tipo_entidad = models.IntegerField(choices=CHOICES1, default=2, blank=True, null=True)
    nombre1 = models.CharField('Primer nombre', default='', blank=True, null=True, max_length=50)
    nombre2 = models.CharField('Segundo nombre', default='', blank=True, null=True, max_length=50)
    apellido1 = models.CharField('Primer apellido', default='', blank=True, null=True, max_length=50)
    apellido2 = models.CharField('Segundo apellido', default='', blank=True, null=True, max_length=50)
    rzn_social = models.CharField('Razón Social', default='', blank=True, null=True, max_length=200)
    tel1 = models.CharField('Teléfono fijo #1', default='', blank=True, null=True, max_length=60)
    tel2 = models.CharField('Teléfono fijo #2', default='', blank=True, null=True, max_length=60)
    celular = models.CharField('Número de celular', default='', blank=True, null=True, max_length=60)
    codigo_postal = models.CharField('Código postal', default='', blank=True, null=True, max_length=10)
    direccion = models.CharField('Dirección', default='', blank=True, null=True, max_length=100)
    email = models.EmailField('E-Mail', blank=True, null=True, max_length=200, default="" )
    home_page = models.URLField('Pagina WEB', blank=True, null=True, max_length=100, default="")
    ciudad = models.ForeignKey(Ciudad, on_delete=models.DO_NOTHING, default=0, null=False, blank=False)
    ret_fuente = models.DecimalField('RETEFUENTE %', max_digits=5, decimal_places=2, default=0, blank=True, null=True)
    ret_ica = models.DecimalField('RETEICA', max_digits=5, decimal_places=2, default=0, blank=True, null=True)
    ret_iva = models.DecimalField('RETEIVA', max_digits=5, decimal_places=2, default=0, blank=True, null=True)
    porc_iva = models.DecimalField('PORCENTAJE IVA', max_digits=5, decimal_places=2, default=0, blank=True, null=True)
    cta_banco = models.CharField('Cuenta Bancaria', default='', blank=True, null=True, max_length=20)
    banco = models.CharField('Nombre Banco Cuenta', default='', blank=True, null=True, max_length=50)
    dias_credito = models.IntegerField(default=0, blank=True, null=True)
    user = models.ForeignKey(User, blank=True, null=True, on_delete=models.DO_NOTHING)

    def __str__(self):
        return '{}: {}'.format(self.rzn_social, self.nit)

    def save(self):
        if self.nombre1:
            self.nombre1 = self.nombre1.upper()
        if self.nombre2:
            self.nombre2 = self.nombre2.upper()
        if self.apellido1:
            self.apellido1 = self.apellido1.upper()
        if self.apellido2:
            self.apellido2 = self.apellido2.upper()
        self.rzn_social = self.rzn_social.upper()
        super(Terceros, self).save()

    class Meta:
        verbose_name_plural = "Terceros"




class Profile(models.Model): 
    user = models.OneToOneField(User, on_delete=models.CASCADE)
    foto = models.FileField("Archivo con Foto del Usuario", upload_to="fotos/", blank=False, null=False)
    logo = models.FileField("Logo del Usuario", upload_to="fotos/", blank=False, null=False)
    nit  = models.CharField('NIT / CC #', blank=False, null=False, max_length=30, default="")
    empresa = models.CharField('Empresa', blank=False, null=False, max_length=100, default="")
    direccion = models.CharField('Direccion Comercial', blank=False, null=False, max_length=100, default="")
    telefono = models.CharField('Telefono Comercial', blank=False, null=False, max_length=100, default="")
    email = models.CharField('Telefono Comercial', blank=False, null=False, max_length=100, default="")
    entrada = models.IntegerField('CONSECUTIVO ENTRADAS DE ALMACEN', default=0, blank=True, null=True)
    salida = models.IntegerField('CONSECUTIVO SALIDAS DE ALMACEN', default=0, blank=True, null=True)
    factura = models.IntegerField('CONSECUTIVO SALIDAS DE ALMACEN', default=0, blank=True, null=True)
    vales = models.IntegerField('CONSECUTIVO VALES', default=0, blank=True, null=True)
    r_dian = models.CharField('Resolucion DIAN', blank=False, null=False, max_length=200, default="")
    cierre = models.IntegerField('CONSECUTIVO CIERRES DIARIOS', default=0, blank=True, null=True)
    tercero_mostrador =  models.ForeignKey(Terceros, on_delete=models.CASCADE)
    #sede = models.ForeignKey(Sedes, on_delete=models.CASCADE, default=0, null=False, blank=False)
 
    def save(self):
        self.empresa = self.empresa.upper()
        super(Profile, self).save()