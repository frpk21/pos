# Generated by Django 2.2.1 on 2022-03-29 15:12

from django.db import migrations, models


class Migration(migrations.Migration):

    dependencies = [
        ('facturas', '0030_pagoscartera'),
    ]

    operations = [
        migrations.AlterField(
            model_name='facturas',
            name='fecha_factura',
            field=models.DateTimeField(),
        ),
    ]
