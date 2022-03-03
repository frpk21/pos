# Generated by Django 2.2.1 on 2022-03-03 18:16

from django.db import migrations, models


class Migration(migrations.Migration):

    dependencies = [
        ('facturas', '0011_formaspagos_slug'),
    ]

    operations = [
        migrations.AddField(
            model_name='facturas',
            name='bonos',
            field=models.DecimalField(decimal_places=2, default=0, max_digits=15),
        ),
        migrations.AddField(
            model_name='facturas',
            name='efectivo',
            field=models.DecimalField(decimal_places=2, default=0, max_digits=15),
        ),
        migrations.AddField(
            model_name='facturas',
            name='tcredito',
            field=models.DecimalField(decimal_places=2, default=0, max_digits=15),
        ),
        migrations.AddField(
            model_name='facturas',
            name='tdebito',
            field=models.DecimalField(decimal_places=2, default=0, max_digits=15),
        ),
        migrations.AddField(
            model_name='facturas',
            name='transferencia',
            field=models.DecimalField(decimal_places=2, default=0, max_digits=15),
        ),
    ]
