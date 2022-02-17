# Generated by Django 2.2.1 on 2022-02-17 22:08

from django.db import migrations, models


class Migration(migrations.Migration):

    dependencies = [
        ('catalogos', '0010_auto_20220216_1217'),
    ]

    operations = [
        migrations.AlterField(
            model_name='producto',
            name='codigo_de_barra',
            field=models.CharField(blank=True, default='', help_text='Código de Barra', max_length=100, null=True, unique=True),
        ),
    ]
