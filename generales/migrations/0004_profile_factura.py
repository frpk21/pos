# Generated by Django 2.2.1 on 2022-02-15 01:01

from django.db import migrations, models


class Migration(migrations.Migration):

    dependencies = [
        ('generales', '0003_auto_20220211_0823'),
    ]

    operations = [
        migrations.AddField(
            model_name='profile',
            name='factura',
            field=models.IntegerField(blank=True, default=0, null=True, verbose_name='CONSECUTIVO SALIDAS DE ALMACEN'),
        ),
    ]
