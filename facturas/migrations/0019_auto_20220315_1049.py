# Generated by Django 2.2.1 on 2022-03-15 15:49

from django.db import migrations, models


class Migration(migrations.Migration):

    dependencies = [
        ('facturas', '0018_facturas_descuento'),
    ]

    operations = [
        migrations.AlterField(
            model_name='formaspagoscierres1',
            name='forma_pago',
            field=models.CharField(help_text='Forma de Pago', max_length=100),
        ),
    ]
