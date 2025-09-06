<?php
/**
 * Usar esta seccion solo en desarrollo para debug
 */
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', true);
date_default_timezone_set('America/Mexico_City');

set_include_path('../library/' . PATH_SEPARATOR . get_include_path());
set_include_path('../application/' . PATH_SEPARATOR . get_include_path());
set_include_path('../application/Controller/' . PATH_SEPARATOR . get_include_path());
set_include_path('../application/Model/' . PATH_SEPARATOR . get_include_path());

require_once('Doctrine/Common/ClassLoader.php');
$classLoader = new \Doctrine\Common\ClassLoader('Doctrine', '../library/');
$classLoader->register(); // register on SPL autoload stack

require_once('Model3/Loader.php');
require_once('Config/constantes.php');
require_once ('dompdf/dompdf_config.inc.php');
Model3_Loader::registerAutoload();
require_once('com.masfletes.db/DBUtil.php');
//inicio de la aplicacion
Model3_Site::initSite('../application/Config/config.ini');
Model3_Site::dispatch(new Model3_Request);

