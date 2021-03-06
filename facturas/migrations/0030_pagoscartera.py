# Generated by Django 2.2.1 on 2022-03-24 17:43

from django.db import migrations, models
import django.db.models.deletion


class Migration(migrations.Migration):

    dependencies = [
        ('facturas', '0029_facturas_tercero'),
    ]

    operations = [
        migrations.CreateModel(
            name='PagosCartera',
            fields=[
                ('id', models.AutoField(auto_created=True, primary_key=True, serialize=False, verbose_name='ID')),
                ('activo', models.BooleanField(default=True, null=True)),
                ('creado', models.DateField(auto_now_add=True, null=True)),
                ('modificado', models.DateField(auto_now=True, null=True)),
                ('fecha', models.DateTimeField()),
                ('pago_no', models.IntegerField(blank=True, default=0, null=True)),
                ('valor_pago', models.DecimalField(decimal_places=2, default=0, max_digits=15)),
                ('saldo_anterior', models.DecimalField(decimal_places=2, default=0, max_digits=15)),
                ('saldo_nuevo', models.DecimalField(decimal_places=2, default=0, max_digits=15)),
                ('factura', models.ForeignKey(on_delete=django.db.models.deletion.CASCADE, to='facturas.Facturas')),
            ],
            options={
                'verbose_name': 'Pago de Cartera',
                'verbose_name_plural': 'Pagos de Cartera',
            },
        ),
    ]
