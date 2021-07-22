from django.http import HttpResponse, HttpResponseRedirect, HttpResponseServerError
from django.urls import reverse, reverse_lazy
from django.contrib.auth.mixins import LoginRequiredMixin
from django.views import generic
from django.contrib.messages.views import SuccessMessageMixin
from facturas.models import Facturas, Factp, HookDian, FactDian
import datetime
from datetime import date
from datetime import datetime, timedelta
from django.shortcuts import redirect
from django.views.generic.base import TemplateView, View
from django.shortcuts import render
from django.contrib import messages
from django.contrib.auth.models import User
from collections import namedtuple
from django.core import serializers
from django.http import JsonResponse
from django.views.decorators.csrf import csrf_exempt, csrf_protect
from django.core.mail import send_mail
from django.conf import settings
from django.core.mail import EmailMessage, EmailMultiAlternatives
from io import StringIO
import os
import requests
import json
from django.http import StreamingHttpResponse
from django.views.decorators.csrf import csrf_exempt
from pos.utilidades import enviar_mail
from json.decoder import JSONDecodeError
from django.http import JsonResponse
from fact_digital.utilidades import verificaEstado



@csrf_exempt
def StatusView(request):
    if request.method == 'POST':
        data = json.loads(request.body)
        if data == None or data == '':
            print('I got a null or empty string value for data in a file')
        else:
            tascodeR = data['event']['tascode']
            if 'alert' in data['event']:
                alertR = data['event']['alert']
            else:
                alertR = ''
            if 'process' in data['event']:
                pR = data['event']['process']
                pcR = data['event']['process']['change']
                pncR = int(data['event']['process']['newValue'])
            else:
                pR = ''
                pcR = False
                pncR = 0
            if 'email' in data['event']:
                emailR = data['event']['email']
                email_eventoR = data['event']['email']['email']
                email_statusR = int(data['event']['email']['status'])
            else:
                emailR = ''
                email_eventoR = ''
                email_statusR = 0
            
            fact = Facturas.objects.get(id=int(data['event']['intID']))

            HookDian.objects.get_or_create(
                factura = fact,
                tascode = tascodeR,
                text = data['event']['text'],
                alert = alertR,
                process = pR,
                process_change = pcR,
                process_newvalue = pncR,
                email = emailR,
                email_evento = email_eventoR,
                email_status = email_statusR,
                sede = fact.sede
            )

            if pncR == 2:
                result_dian = verificaEstado(tascodeR)
                FactDian.objects.get_or_create(
                    factura = fact,
                    modes = result_dian['invoiceResult']['document']['mode'],
                    tascode = tascodeR,
                    intid = result_dian['invoiceResult']['document']['intID'],
                    process = result_dian['invoiceResult']['document']['process'],
                    retries = result_dian['invoiceResult']['document']['retries'],
                    cufe = result_dian['invoiceResult']['document']['CUFE'],
                    url = result_dian['invoiceResult']['document']['URL'],
                    pdf = result_dian['invoiceResult']['document']['PDF'],
                    attached = result_dian['invoiceResult']['document']['ATTACHED'],
                    sede = fact.sede
                )

            return JsonResponse(data)

    return HttpResponse('Hello', content_type='text/plain', status=410)
