# Generated by Django 2.2.1 on 2021-12-17 00:57

from django.conf import settings
from django.db import migrations, models
import django.db.models.deletion


class Migration(migrations.Migration):

    initial = True

    dependencies = [
        migrations.swappable_dependency(settings.AUTH_USER_MODEL),
        ('generales', '0001_initial'),
    ]

    operations = [
        migrations.CreateModel(
            name='Categoria',
            fields=[
                ('id', models.AutoField(auto_created=True, primary_key=True, serialize=False, verbose_name='ID')),
                ('activo', models.BooleanField(default=True, null=True)),
                ('creado', models.DateField(auto_now_add=True, null=True)),
                ('modificado', models.DateField(auto_now=True, null=True)),
                ('nombre', models.CharField(help_text='Nombre de la categoría', max_length=100, unique=True)),
                ('usuario', models.ForeignKey(blank=True, null=True, on_delete=django.db.models.deletion.DO_NOTHING, to=settings.AUTH_USER_MODEL)),
            ],
            options={
                'verbose_name_plural': 'Categorias',
            },
        ),
        migrations.CreateModel(
            name='Iva',
            fields=[
                ('id', models.AutoField(auto_created=True, primary_key=True, serialize=False, verbose_name='ID')),
                ('activo', models.BooleanField(default=True, null=True)),
                ('creado', models.DateField(auto_now_add=True, null=True)),
                ('modificado', models.DateField(auto_now=True, null=True)),
                ('tarifa_iva', models.IntegerField(default=19)),
            ],
            options={
                'verbose_name_plural': 'TARIFAS IVA',
                'unique_together': {('tarifa_iva',)},
            },
        ),
        migrations.CreateModel(
            name='Movimientos',
            fields=[
                ('id', models.AutoField(auto_created=True, primary_key=True, serialize=False, verbose_name='ID')),
                ('activo', models.BooleanField(default=True, null=True)),
                ('creado', models.DateField(auto_now_add=True, null=True)),
                ('modificado', models.DateField(auto_now=True, null=True)),
                ('fecha', models.DateTimeField(verbose_name='Fecha documento')),
                ('valor_documento', models.DecimalField(blank=True, decimal_places=0, default=0, max_digits=12, null=True, verbose_name='Valor documento')),
                ('tercero', models.ForeignKey(on_delete=django.db.models.deletion.CASCADE, to='generales.Terceros')),
            ],
            options={
                'verbose_name_plural': 'Movimientos',
            },
        ),
        migrations.CreateModel(
            name='Tipos_movimientos',
            fields=[
                ('id', models.AutoField(auto_created=True, primary_key=True, serialize=False, verbose_name='ID')),
                ('activo', models.BooleanField(default=True, null=True)),
                ('creado', models.DateField(auto_now_add=True, null=True)),
                ('modificado', models.DateField(auto_now=True, null=True)),
                ('nombre', models.CharField(help_text='Nombre Tipo de Movimiento', max_length=100, unique=True)),
                ('tipo', models.IntegerField(default=1)),
            ],
            options={
                'verbose_name_plural': 'Tipos de Movimientos',
            },
        ),
        migrations.CreateModel(
            name='Ubicaciones',
            fields=[
                ('id', models.AutoField(auto_created=True, primary_key=True, serialize=False, verbose_name='ID')),
                ('activo', models.BooleanField(default=True, null=True)),
                ('creado', models.DateField(auto_now_add=True, null=True)),
                ('modificado', models.DateField(auto_now=True, null=True)),
                ('descripcion', models.CharField(help_text='Descripción de la Ubicación', max_length=100)),
                ('direccion', models.CharField(help_text='Dirección', max_length=100)),
                ('ciudad', models.ForeignKey(default=0, on_delete=django.db.models.deletion.CASCADE, to='generales.Ciudad')),
                ('usuario', models.ForeignKey(blank=True, null=True, on_delete=django.db.models.deletion.DO_NOTHING, to=settings.AUTH_USER_MODEL)),
            ],
            options={
                'verbose_name_plural': 'Ubicaciones',
                'unique_together': {('direccion', 'descripcion')},
            },
        ),
        migrations.CreateModel(
            name='SubCategoria',
            fields=[
                ('id', models.AutoField(auto_created=True, primary_key=True, serialize=False, verbose_name='ID')),
                ('activo', models.BooleanField(default=True, null=True)),
                ('creado', models.DateField(auto_now_add=True, null=True)),
                ('modificado', models.DateField(auto_now=True, null=True)),
                ('nombre', models.CharField(help_text='Descripción de la sub categoría', max_length=100)),
                ('categoria', models.ForeignKey(on_delete=django.db.models.deletion.CASCADE, to='catalogos.Categoria')),
            ],
            options={
                'verbose_name_plural': 'Sub Categorias',
                'unique_together': {('categoria', 'nombre')},
            },
        ),
        migrations.CreateModel(
            name='Producto',
            fields=[
                ('id', models.AutoField(auto_created=True, primary_key=True, serialize=False, verbose_name='ID')),
                ('activo', models.BooleanField(default=True, null=True)),
                ('creado', models.DateField(auto_now_add=True, null=True)),
                ('modificado', models.DateField(auto_now=True, null=True)),
                ('nombre', models.CharField(help_text='Nombre del producto', max_length=100)),
                ('descripcion', models.CharField(help_text='Descripción del producto', max_length=100)),
                ('archivo_foto', models.FileField(blank=True, default='', null=True, upload_to='fotos/')),
                ('unidad_de_medida', models.IntegerField(choices=[(1, 'UNIDAD'), (2, 'KILOGRAMO'), (3, 'GRAMO'), (4, 'MILIGRAMO'), (5, 'METRO'), (6, 'CENTIMETRO'), (7, 'MILIMETRO'), (8, 'LITRO'), (9, 'MILILITRO'), (10, 'CENTILITRO'), (11, 'METRO CUADRADO'), (12, 'CENTIMETRO CUADRADO'), (13, 'LITRO')], default=1)),
                ('existencia', models.DecimalField(blank=True, decimal_places=2, default=0, max_digits=12, null=True, verbose_name='EXISTENCIA')),
                ('stock_minimo', models.DecimalField(blank=True, decimal_places=0, default=0, max_digits=10, null=True, verbose_name='STOCK MINIMO')),
                ('stock_maximo', models.DecimalField(blank=True, decimal_places=0, default=0, max_digits=10, null=True, verbose_name='STOCK MAXIMO')),
                ('total_copas', models.DecimalField(blank=True, decimal_places=0, default=0, max_digits=10, null=True, verbose_name='TOTAL COPAS')),
                ('descuento_promo', models.DecimalField(blank=True, decimal_places=0, default=0, max_digits=2, null=True, verbose_name='DESCUENTO PROMOCIONAL')),
                ('costo_unidad', models.DecimalField(blank=True, decimal_places=2, default=0, max_digits=12, null=True, verbose_name='VALOR COMPRA')),
                ('precio_de_venta', models.DecimalField(blank=True, decimal_places=0, default=0, max_digits=12, null=True, verbose_name='PRECIO DE VENTA')),
                ('fecha_de_vencimiento', models.DateTimeField(blank=True, null=True, verbose_name='Fecha de Vencimiento')),
                ('codigo_de_barra', models.CharField(blank=True, default='', help_text='Código de Barra', max_length=100, null=True)),
                ('cuenta_contable_ventas_locales', models.CharField(blank=True, default='', max_length=20, null=True, verbose_name='Cuenta de contabilidad para ventas locales')),
                ('cuenta_contable_ventas_exterior', models.CharField(blank=True, default='', max_length=20, null=True, verbose_name='Cuenta de contabilidad para ventas al exterior')),
                ('proveedor', models.ManyToManyField(related_name='Proveedores_del_producto', to='generales.Terceros')),
                ('subcategoria', models.ForeignKey(on_delete=django.db.models.deletion.CASCADE, to='catalogos.SubCategoria')),
                ('tarifa_iva', models.ForeignKey(on_delete=django.db.models.deletion.DO_NOTHING, to='catalogos.Iva')),
                ('ubicacion', models.ForeignKey(default=1, on_delete=django.db.models.deletion.DO_NOTHING, to='catalogos.Ubicaciones')),
                ('usuario', models.ForeignKey(blank=True, null=True, on_delete=django.db.models.deletion.DO_NOTHING, to=settings.AUTH_USER_MODEL)),
            ],
            options={
                'verbose_name_plural': 'Productos',
            },
        ),
        migrations.CreateModel(
            name='Movimientos_detalle',
            fields=[
                ('id', models.AutoField(auto_created=True, primary_key=True, serialize=False, verbose_name='ID')),
                ('activo', models.BooleanField(default=True, null=True)),
                ('creado', models.DateField(auto_now_add=True, null=True)),
                ('modificado', models.DateField(auto_now=True, null=True)),
                ('producto', models.CharField(blank=True, default='', help_text='Nombre del producto', max_length=100, null=True)),
                ('codigo_de_barra', models.CharField(blank=True, default='', help_text='Código de Barra', max_length=100, null=True)),
                ('cantidad', models.DecimalField(blank=True, decimal_places=2, default=0, max_digits=12, null=True, verbose_name='Cantidad')),
                ('costo', models.DecimalField(blank=True, decimal_places=0, default=0, max_digits=12, null=True, verbose_name='Precio de Compra')),
                ('total', models.DecimalField(blank=True, decimal_places=0, default=0, max_digits=12, null=True, verbose_name='Total')),
                ('movimiento', models.ForeignKey(on_delete=django.db.models.deletion.CASCADE, to='catalogos.Movimientos')),
            ],
            options={
                'verbose_name_plural': 'Movimientos Detalles',
            },
        ),
        migrations.AddField(
            model_name='movimientos',
            name='tipo',
            field=models.ForeignKey(on_delete=django.db.models.deletion.CASCADE, to='catalogos.Tipos_movimientos'),
        ),
        migrations.AddField(
            model_name='movimientos',
            name='ubicacion',
            field=models.ForeignKey(default=1, on_delete=django.db.models.deletion.DO_NOTHING, to='catalogos.Ubicaciones'),
        ),
        migrations.AddField(
            model_name='movimientos',
            name='usuario',
            field=models.ForeignKey(blank=True, null=True, on_delete=django.db.models.deletion.DO_NOTHING, to=settings.AUTH_USER_MODEL),
        ),
    ]
