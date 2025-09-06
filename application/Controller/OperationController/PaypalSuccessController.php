<?php
session_start();

$_SESSION["paypal"] = $_REQUEST;
$urlCompraCreditos = $_REQUEST["urlRet"];
header('Location:'.$urlCompraCreditos.'/fncPayPal'); 


  

