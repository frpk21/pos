#rangos de facturacion de una cuenta

import requests
import json

LF_GENERAL="/general/"
LF_INVOICE="/invoice/"

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
    return getInfo_LF(url,LF_INVOICE,"DELETE",usr,pwd,info)





# Datos basicos
USR = "usr-fc6a96f1371fef8cd18e9e649c45cf6f-v300"
PWD = "pwd-472e053a2b24c946e6185525e2fcceeax-v300"
URL = "https://play.tas-la.com/facturacion.v30"

# Solicitud
solicitud= {
              'getRanges':{
                'mode':'active',
                'type':'all'
              }
            }

# Envio
response  = getRanges(URL,USR,PWD,solicitud)
# Resultado
print(response.text)