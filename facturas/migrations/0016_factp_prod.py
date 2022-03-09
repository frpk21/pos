# Generated by Django 2.2.1 on 2022-03-08 16:58

from django.db import migrations, models
import django.db.models.deletion


class Migration(migrations.Migration):

    dependencies = [
        ('catalogos', '0013_auto_20220217_1748'),
        ('facturas', '0015_facturas_con_electronica'),
    ]

    operations = [
        migrations.AddField(
            model_name='factp',
            name='prod',
            field=models.ForeignKey(default=12, on_delete=django.db.models.deletion.CASCADE, to='catalogos.Producto'),
            preserve_default=False,
        ),
    ]
