@echo off
echo Sitio Flexuministros con panel administrativo (base de datos)
echo.
echo Abra su navegador en:  http://localhost:8090
echo Panel administrativo:   http://localhost:8090/admin
echo.
echo Deje esta ventana abierta mientras lo use. Para detenerlo, cierre esta ventana.
echo.
"C:\xampp\php\php.exe" -S localhost:8090 -t "%~dp0."
pause
