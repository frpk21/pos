  
#rangos de facturacion de una cuenta

import fact_digital.api as api

# Datos basicos
USR = "elusuario"
PWD = "lacontrasena"
URL = "https://play.tas-la.com/facturacion.v30"

# Solicitud
solicitud= {
              'getEmail':{
                'id':'900900900'
              }
            }

# Envio
response  = api.getEmail(URL,USR,PWD,solicitud)
# Resultado
print(response.text)