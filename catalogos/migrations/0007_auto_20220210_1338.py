# Generated by Django 2.2.1 on 2022-02-10 18:38

from django.db import migrations


class Migration(migrations.Migration):

    dependencies = [
        ('catalogos', '0006_auto_20220210_1322'),
    ]

    operations = [
        migrations.RenameField(
            model_name='formulacion',
            old_name='producto_a_formular',
            new_name='producto',
        ),
    ]