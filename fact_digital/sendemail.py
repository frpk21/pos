
# Log de eventos de una factura

import fact_digital.api as api

# Datos basicos
USR = "elusuario"
PWD = "lacontrasena"
URL = "https://play.tas-la.com/facturacion.v30"

# Solicitud
solicitud= {
              'sendEmail':{
                'tascode':'xxx',
                "email":"ejemplo@ejemplo.com"
              }
            }

# Envio
response  = api.sendEmail(URL,USR,PWD,solicitud)
# Resultado
print(response.text)