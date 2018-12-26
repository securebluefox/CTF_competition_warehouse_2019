for /R . %%i in (*.dll) do regsvr32 /s /u %%i
pause