# Generated by Django 2.2.1 on 2022-02-28 15:02

from django.db import migrations, models


class Migration(migrations.Migration):

    dependencies = [
        ('generales', '0004_profile_factura'),
    ]

    operations = [
        migrations.AddField(
            model_name='profile',
            name='r_dian',
            field=models.CharField(default='', max_length=200, verbose_name='Resolucion DIAN'),
        ),
    ]