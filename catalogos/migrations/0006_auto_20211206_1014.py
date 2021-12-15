# Generated by Django 2.2.1 on 2021-12-06 15:14

import datetime
from django.conf import settings
from django.db import migrations, models
import django.db.models.deletion


class Migration(migrations.Migration):

    dependencies = [
        migrations.swappable_dependency(settings.AUTH_USER_MODEL),
        ('generales', '0005_auto_20211206_1014'),
        ('catalogos', '0005_remove_producto_lote'),
    ]

    operations = [
        migrations.CreateModel(
            name='Movimientos',
            fields=[
                ('id', models.AutoField(auto_created=True, primary_key=True, serialize=False, verbose_name='ID')),
                ('activo', models.BooleanField(default=True, null=True)),
                ('creado', models.DateField(auto_now_add=True, null=True)),
                ('modificado', models.DateField(auto_now=True, null=True)),
                ('fecha', models.DateTimeField(default=datetime.datetime(2021, 12, 6, 10, 14, 46, 864551), verbose_name='Fecha documento')),
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
        migrations.RemoveField(
            model_name='producto',
            name='proveedor',
        ),
        migrations.AddField(
            model_name='producto',
            name='proveedor',
            field=models.ManyToManyField(related_name='Proveedores_del_producto', to='generales.Terceros'),
        ),
        migrations.CreateModel(
            name='Movimientos_detalle',
            fields=[
                ('id', models.AutoField(auto_created=True, primary_key=True, serialize=False, verbose_name='ID')),
                ('activo', models.BooleanField(default=True, null=True)),
                ('creado', models.DateField(auto_now_add=True, null=True)),
                ('modificado', models.DateField(auto_now=True, null=True)),
                ('cantidad', models.DecimalField(blank=True, decimal_places=2, default=0, max_digits=12, null=True, verbose_name='Cantidad')),
                ('costo', models.DecimalField(blank=True, decimal_places=0, default=0, max_digits=12, null=True, verbose_name='Precio de Compra')),
                ('movimiento', models.ForeignKey(on_delete=django.db.models.deletion.CASCADE, to='catalogos.Movimientos')),
                ('producto', models.ForeignKey(on_delete=django.db.models.deletion.CASCADE, to='catalogos.Producto')),
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
