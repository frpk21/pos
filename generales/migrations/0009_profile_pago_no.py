# Generated by Django 2.2.1 on 2022-03-24 17:42

from django.db import migrations, models


class Migration(migrations.Migration):

    dependencies = [
        ('generales', '0008_profile_tercero_mostrador'),
    ]

    operations = [
        migrations.AddField(
            model_name='profile',
            name='pago_no',
            field=models.IntegerField(blank=True, default=0, null=True, verbose_name='CONSECUTIVO PAGOS CARTERA'),
        ),
    ]