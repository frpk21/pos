#Verificar el estado de un documento

import fact_digital.api as api
# Datos basicos
USR = "usr-fc6a96f1371fef8cd18e9e649c45cf6f-v300"
PWD = "pwd-472e053a2b24c946e6185525e2fcceeax-v300"
URL = "https://play.tas-la.com/facturacion.v30"

# Solicitud
solicitud= {
              'verifyStatus':{
                'tascode':'xxx'
              }
            }

# Envio
response  = api.verifyStatus(URL,USR,PWD,solicitud)
# Resultado
print(response.text)