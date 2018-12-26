for /R . %%i in (*.dll) do regsvr32 /s %%i
pause