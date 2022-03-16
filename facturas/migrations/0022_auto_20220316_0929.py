# Generated by Django 2.2.1 on 2022-03-16 14:29

from django.db import migrations, models


class Migration(migrations.Migration):

    dependencies = [
        ('facturas', '0021_cierres_cierre_no'),
    ]

    operations = [
        migrations.RemoveField(
            model_name='cierres1',
            name='base_caja',
        ),
        migrations.RemoveField(
            model_name='cierres1',
            name='total_venta',
        ),
        migrations.AddField(
            model_name='cierres',
            name='base_caja',
            field=models.DecimalField(decimal_places=2, default=0, max_digits=15),
        ),
    ]