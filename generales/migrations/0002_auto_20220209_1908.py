# Generated by Django 2.2.1 on 2022-02-10 00:08

from django.db import migrations, models


class Migration(migrations.Migration):

    dependencies = [
        ('generales', '0001_initial'),
    ]

    operations = [
        migrations.AddField(
            model_name='profile',
            name='entrada',
            field=models.IntegerField(blank=True, default=0, null=True, verbose_name='CONSECUTIVO ENTRADAS DE ALMACEN'),
        ),
        migrations.AddField(
            model_name='profile',
            name='salida',
            field=models.IntegerField(blank=True, default=0, null=True, verbose_name='CONSECUTIVO SALIDAS DE ALMACEN'),
        ),
    ]
