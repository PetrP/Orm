@echo off
SET /P dir=Zadejte adresar: 
mklink /J "%dir%" Orm

pause
