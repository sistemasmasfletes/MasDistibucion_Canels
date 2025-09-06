@echo off

if "%PHP_BIN%" == "" set PHPBIN=@php_bin@
if not exist "%PHP_BIN%" if "%PHP_PEAR_PHP_BIN%" neq "" goto USE_PEAR_PATH
GOTO RUN
:USE_PEAR_PATH
set PHPBIN=%PHP_PEAR_PHP_BIN%
:RUN
"%PHP_BIN%" "doctrine" %*
