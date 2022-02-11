# Generated by Django 2.2.1 on 2022-02-11 13:23

from django.db import migrations, models


class Migration(migrations.Migration):

    dependencies = [
        ('generales', '0002_auto_20220209_1908'),
    ]

    operations = [
        migrations.AlterField(
            model_name='profile',
            name='foto',
            field=models.FileField(upload_to='fotos/', verbose_name='Archivo con Foto del Usuario'),
        ),
        migrations.AlterField(
            model_name='profile',
            name='logo',
            field=models.FileField(upload_to='fotos/', verbose_name='Logo del Usuario'),
        ),
    ]