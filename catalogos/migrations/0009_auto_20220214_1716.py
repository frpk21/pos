# Generated by Django 2.2.1 on 2022-02-14 22:16

from django.db import migrations, models


class Migration(migrations.Migration):

    dependencies = [
        ('catalogos', '0008_auto_20220214_1708'),
    ]

    operations = [
        migrations.AlterField(
            model_name='tipos_movimientos',
            name='nombre',
            field=models.CharField(help_text='Nombre Tipo de Movimiento', max_length=100),
        ),
    ]
