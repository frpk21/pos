# Generated by Django 2.2.1 on 2022-03-15 16:39

from django.db import migrations, models


class Migration(migrations.Migration):

    dependencies = [
        ('generales', '0005_profile_r_dian'),
    ]

    operations = [
        migrations.AddField(
            model_name='profile',
            name='cierre',
            field=models.IntegerField(blank=True, default=0, null=True, verbose_name='CONSECUTIVO CIERRES DIARIOS'),
        ),
    ]