"""
    Script de demostración de uso. Recuerda que debes configurar la impresora y el conector.
    1. Descarga el conector:  https://github.com/parzibyte/ejemplos-plugin-impresoras-termicas-v2/releases/latest
    2. Configura tu impresora y compártela
    3. Ejecuta el conector (no abre ventanas)
    4. Ejecuta este script.
    Ante cualquier duda: https://parzibyte.me/blog/2021/02/09/presentando-plugin-impresoras-termicas-version-2/
"""
"""
from conectorplugin import Conector, AccionBarcodeJan13, AlineacionCentro

# Esto es para obtener las impresoras. No es obligatorio hacerlo siempre que se quiera imprimir
impresoras = Conector.obtenerImpresoras()
print(f"Las impresoras son: {impresoras}")
ejemplo="Año 2021"
c = Conector()
c.textoConAcentos("¡Me llamo María José!\n")
c.establecerEnfatizado(1)
c.texto(ejemplo+"\n")
c.establecerEnfatizado(0)
c.texto("Sin enfatizado\n")
c.establecerTamanioFuente(2, 2)
c.texto("Texto de 2, 2\n")
c.establecerTamanioFuente(1, 1)
c.establecerJustificacion(AlineacionCentro)
c.texto("Texto centrado\n")
c.texto("Código de barras:\n")
c.codigoDeBarras("7506129445966", AccionBarcodeJan13)
c.qrComoImagen("Parzibyte")
c.texto("Imagen de URL:\n")
#c.imagenDesdeUrl("https://github.com/parzibyte.png")
c.texto("Imagen local:\n")
# Recuerda que la imagen debe existir y debe ser legible para el plugin. Si no, comenta las líneas
#c.imagenLocal(
#    "C:\\Users\\Luis Cabrera Benito\\Desktop\\cliente_python_impresoras_termicas\\python-logo.png")
c.feed(5)
c.cortar()
c.abrirCajon()
print("Imprimiendo...")
# Recuerda cambiar por el nombre de tu impresora
respuesta = c.imprimirEn("POS-90")
if respuesta == True:
    print("Impresión correcta")
else:
    print(f"Error. El mensaje es: {respuesta}")
    
    
"""

import os, sys, win32api
from tkinter.font import BOLD

def archivoImprime(di, me, an, texto):
    fecha=str(an)+str(me)+str(di)
    fecha=str(fecha+".txt")
    escritura=open(fecha,"w")
    escritura.write(texto+"\n")
    escritura.write("texto\b")
    escritura.close()
    import win32print
    
#    print(texto)
    os.startfile(fecha,"print")
    fname="C:\\somePDF.pdf"
    p = win32print.OpenPrinter ('POS-90')
    job = win32print.StartDocPrinter (p, 1, ("test of raw data", None, "RAW")) 
    win32print.StartPagePrinter (p) 
    win32print.WritePrinter (p, job) 
    win32print.EndPagePrinter (p)
   # win32api.ShellExecute(0, 'open', 'gsprint.exe', '-printer "\\\\localhost\\POS-90"D:\python\proyecto\pos\facturas\cot.pdf', '.', 0)
   # win32api.ShellExecute(0, 'open', 'gsprint.exe', '-printer "\\\\' + self.server + '\\' + self.printer_name + '" ' + file, '.', 0)
    
archivoImprime(0,0,0,"prueba de impresionxxxxxxxxxxxxxxx")

