import requests
import json
import datetime
from datetime import date
import hashlib

# Datos basicos
LF_GENERAL="/general/"
LF_INVOICE="/invoice/"


# PARAMETRO PARA MODO PRUEBAS:
USR = "usr-38717bdca536b4f02ec8bd20e941d548-v300"
PWD = "pwd-6cc499d5968c5290e90803e5c486c5fcx-v300"
URL = "https://play.tas-la.com/facturacion.v30"


# PARAMETRO PARA MODO PRODUCCION:
#USR = "usr-391fbbcfae39a975fa294667a832ae94-v300"
#PWD = "pwd-f3b5a9aae14c8a6dd4db96d6ba39c718x-v300"
#URL = "https://wrk.tas-la.com/facturacion.v30"


def getInfo_LF(url, service, method, usr, pwd, info):
    return requests.request(method,url+service,json=info,headers={"content-type": "application/json"},auth=(usr,pwd))

# Funcion para obtener rangos
def getRanges(url,usr,pwd,info):
    return getInfo_LF(url,LF_GENERAL,"GET",usr,pwd,info)

# Funcion para obtuener email de la DIAN
def getEmail(url,usr,pwd,info):
    return getInfo_LF(url,LF_GENERAL,"GET",usr,pwd,info)

# Funcion para obtuener el registro mercantl del RUES
def getRUESRM(url,usr,pwd,info):
    return getInfo_LF(url,LF_GENERAL,"GET",usr,pwd,info)

# Funcion para buscar por nombre en el RUES
def getRUESSearch(url,usr,pwd,info):
    return getInfo_LF(url,LF_GENERAL,"GET",usr,pwd,info)

#Envio de email a cliente
def sendEmail(url,usr,pwd,info):
    return getInfo_LF(url,LF_GENERAL,"GET",usr,pwd,info)

# Funcion para enviar una factura
def invoice(url,usr,pwd,info):
    return getInfo_LF(url,LF_INVOICE,"POST",usr,pwd,info)

# Funcion para obtener el log de un documento
def getLog(url,usr,pwd,info):
    return getInfo_LF(url,LF_INVOICE,"GET",usr,pwd,info)

# Funcion para  verificar el estado de un documento
def verifyStatus(url,usr,pwd,info):
    return getInfo_LF(url,LF_INVOICE,"GET",usr,pwd,info)

# Funcion para anular una factura
def deleteInvoice(url,usr,pwd,info):
    return getInfo_LF(url,"/creditNote/","DELETE",usr,pwd,info)

def fechaDian(fecha):
    ano = str(fecha.year)
    mes = str(fecha.month)
    if len(mes) < 2:
        mes = '0'+mes[0]
    dia = str(fecha.day)
    if len(dia) < 2:
        dia = '0'+dia
    return(ano+mes+dia)

def HoraDian():
    hh = str(datetime.datetime.today().hour)
    mm = str(datetime.datetime.today().minute)
    ss = str(datetime.datetime.today().second)
    if len(hh)<2:
        hh = '0' + hh
    if len(mm)<2:
        mm = '0' + mm
    if len(ss)<2:
        ss = '0' + ss
    return(hh+mm+ss)


def consultaRangos():
    solicitud= {
        'getRanges':{
        'mode':'active',
        'type':'all'
        }
    }
    # Envio
    response  = getRanges(URL,USR,PWD,solicitud)
    # Resultado
    return(response.json())


def verificaEstado(tascodeR):
    solicitud= {
        'verifyStatus':{
            'tascode': tascodeR
            }
        }
    # Envio
    response  = verifyStatus(URL,USR,PWD,solicitud)
    # Resultado
    return(response.json())




def AnulaFactura(tascodeR,fechaR,motivoR):
    hoy = date.today()
    factura = tascodeR+fechaDian(hoy)
    print(factura)
    hashR = hashlib.md5(factura.encode("utf-8")).hexdigest()
    #hashR = hashlib.md5(tascodeR+hoy.strftime("%Y%m%d")).hexdigest()
    solicitud= {
        'deleteInvoice': {
            'tascode': tascodeR,
            'hash': hashR,
            'description': 'Cliente No Acepta'
        }
    }

    # Envio
    response  = deleteInvoice(URL,USR,PWD,solicitud)
    # Resultado
    return(response.json())





def facturaDian(data_dian):
    if int(float(data_dian['taxAmount']))>0:
        solicitud= {
            "invoice":{
            "rangeKey": data_dian['rangeKey'],
            "intID":data_dian['intID'],
            "issueDate":data_dian['issueDate'],
            "issueTime":data_dian['issueTime'],
            "dueDate":data_dian['dueDate'],
            "note1":data_dian['note1'],
            "note2":data_dian['note2'],
            "customer":{
                "additionalAccountID":data_dian['additionalAccountID'],
                "name":data_dian['name'],
                "city":data_dian['city'],
                "countryEntity":data_dian['countryEntity'],
                "countrySubentity":data_dian['countrySubentity'],
                "addressLine":data_dian['addressLine'],
                "documentNumber":data_dian['documentNumber'],
                "documentType":data_dian['documentType'],
                "telephone":data_dian['telephone'],
                "email":data_dian['email']
                },
                "amounts":{
                    "totalAmount":data_dian['totalAmount'],
                    "discountAmount":data_dian['discountAmount'],
                    "extraAmount":data_dian['extraAmount'],
                    "taxAmount":data_dian['taxAmount'],
                    "payAmount":data_dian['payAmount']
                },
                "items":[
                    {
                        "quantity":"1.00",
                        "unitPrice":data_dian['unitPrice'],
                        "total":data_dian['total'],
                        "description":data_dian['description'],
                        "brand":data_dian['brand'],
                        "model":data_dian['model'],
                        "standard":"999",
                        "agency":"991",
                        "code":"1000",
                        "allowance":[
                            {
                                "charge":"false",
                                "reasonCode":"11",
                                "description":"N/A",
                                "baseAmount":"0.00",
                                "amount":"0.00",
                                "percent":"0.00"
                            }
                        ],
                        "taxes":[
                            {
                                "ID":data_dian['ID'],
                                "taxAmount":data_dian['taxAmount'],
                                "percent":"19.00"
                            }
                        ]
                    }
                ]
            }
        }
    else:
        solicitud= {
            "invoice":{
            "rangeKey": data_dian['rangeKey'],
            "intID":data_dian['intID'],
            "issueDate":data_dian['issueDate'],
            "issueTime":data_dian['issueTime'],
            "dueDate":data_dian['dueDate'],
            "note1":data_dian['note1'],
            "note2":data_dian['note2'],
            "customer":{
                "additionalAccountID":data_dian['additionalAccountID'],
                "name":data_dian['name'],
                "city":data_dian['city'],
                "countryEntity":data_dian['countryEntity'],
                "countrySubentity":data_dian['countrySubentity'],
                "addressLine":data_dian['addressLine'],
                "documentNumber":data_dian['documentNumber'],
                "documentType":data_dian['documentType'],
                "telephone":data_dian['telephone'],
                "email":data_dian['email']
                },
                "amounts":{
                    "totalAmount":data_dian['totalAmount'],
                    "discountAmount":data_dian['discountAmount'],
                    "extraAmount":data_dian['extraAmount'],
                    "payAmount":data_dian['payAmount']
                },
                "items":[
                    {
                        "quantity":"1.00",
                        "unitPrice":data_dian['unitPrice'],
                        "total":data_dian['total'],
                        "description":data_dian['description'],
                        "brand":data_dian['brand'],
                        "model":data_dian['model'],
                        "standard":"999",
                        "agency":"991",
                        "code":"1000",
                        "allowance":[
                            {
                                "charge":"false",
                                "reasonCode":"11",
                                "description":"N/A",
                                "baseAmount":"0.00",
                                "amount":"0.00",
                                "percent":"0.00"
                            }
                        ]
                    }
                ]
            }
        }
    # Envio
    response  = invoice(URL,USR,PWD,solicitud)
    # Resultado
    return(response.json())


