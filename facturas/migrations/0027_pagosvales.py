# Generated by Django 2.2.1 on 2022-03-18 22:36

from django.db import migrations, models
import django.db.models.deletion


class Migration(migrations.Migration):

    dependencies = [
        ('facturas', '0026_vales_cerrado'),
    ]

    operations = [
        migrations.CreateModel(
            name='PagosVales',
            fields=[
                ('id', models.AutoField(auto_created=True, primary_key=True, serialize=False, verbose_name='ID')),
                ('activo', models.BooleanField(default=True, null=True)),
                ('creado', models.DateField(auto_now_add=True, null=True)),
                ('modificado', models.DateField(auto_now=True, null=True)),
                ('fecha', models.DateTimeField()),
                ('valor_pago', models.DecimalField(decimal_places=2, default=0, max_digits=15)),
                ('vales', models.ForeignKey(on_delete=django.db.models.deletion.CASCADE, to='facturas.Vales')),
            ],
            options={
                'verbose_name': 'Pagos Vales',
                'verbose_name_plural': 'Pago Vale',
            },
        ),
    ]
