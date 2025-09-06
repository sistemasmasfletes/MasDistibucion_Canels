<?php
use com\masfletes\db\DBUtil;include 'PHPMailer/phpmailer.php';include 'PHPMailer/smtp.php';
/**
 * Clase Page, para el M3
 *
 * Esta clase obtiene todas las acciones del controlador de pagina
 * @package Model3
 * @author Hector Benitez
 * @version 0.3
 */
abstract class Model3_Controller
{

    /**
     *
     * @var Model3_View
     */
    protected $view;

    /**
     *
     * @var Model3_Request
     */
    protected $_request;

    /*@var Boolean*/
    protected $responseJSON;
    

    private $defaultDb = "DefaultDb";

    public function __construct($request)
    {
        $this->view = new Model3_View($request);

        $this->_request = $request;
        if ($request->isComponent())
        {
            $this->view->setBaseUrlPublic($this->view->getBaseUrl() . '_components/' . $request->getComponent() . '/');
            $this->view->setBaseUrl($this->view->getBaseUrl() . $request->getComponent() . '/');
        }
        $this->responseJSON = FALSE;
    }

    public function init()
    {

    }

    public function postDispatch()
    {

    }

    public function preDispatch()
    {

    }

    /**
     * Esta clase carga el despachador de acciones
     * @param $action La accion a cargar
     * @return bool Regresa true si la accion fue cargada , caso contrario false
     */
    public function dispatch()
    {
        $method = $this->_request->getAction() . 'Action';

        if (method_exists($this, $method))
        {
            $this->preDispatch();
            $this->$method();
            $this->postDispatch();
            return true;
        } else
        {
            throw new Exception("Action '{$method}' is not defined in class " . get_class($this));
        }
        return false;
    }

    public function getView()
    {
        return $this->view;
    }

    /**
     *
     * @return Model3_Request
     */
    public function getRequest()
    {
        return $this->_request;
    }
    
    public function isResponseJSON(){
        return $this->responseJSON;
    }

    public function redirect($path = '', $useBaseUrl = true, $permanent = false)
    {
        $fullPath = $path;
        
        $config = Model3_Registry::get('config');
        $configData = $config->getArray();
        if ($configData['m3_internationalization']['inter_multilang'] == true)
        {
            $fullPath = $configData['m3_internationalization']['inter_default_lang'] . '/' . $path;
        }
        
        if($useBaseUrl == true)
            $fullPath = $this->_request->getBaseUrl() . '/' . $fullPath;
        if($permanent == true)
        {
            header("HTTP/1.1 301 Moved Permanently");
        }

        header('Location: ' . $fullPath);
        exit;
    }

    /**
     *
     * @param string $dbName
     * @return Doctrine\ORM\EntityManager
     */
    public function getEntityManager($dbName)
    {
        $em = false;
        $dbs = Model3_Registry::getInstance()->get('databases');
        if(array_key_exists($dbName, $dbs) == true)
            $em = $dbs[$dbName];
        return $em;
    }

    public function hasPermission($sessionId,$pantalla,$permiso){        
      if(!DBUtil::validarPermiso($this->getConnection(), $sessionId,$pantalla,$permiso)){
            $this->createHttpResponse(403);
            exit;
      }
    }

    public function hasPermissionM3($sessionId,$pantalla,$permiso){
      return DBUtil::validarPermiso($this->getConnection(), $sessionId,$pantalla,$permiso);           
    }


    /**
     *
     * @param string $dbName
     * @return Doctrine\ORM\EntityManager Returns the underlying connection from DBAL
     * 
     */    
    public function getConnection()
    {
        $em = false;
        $dbs = Model3_Registry::getInstance()->get('databases');
        if(array_key_exists($this->defaultDb, $dbs) == true)
            $em = $dbs[$this->defaultDb];
        if($em)
            return $em->getConnection()->getWrappedConnection();
        else
            return null;
    }

    public function createHttpResponse($code = NULL){
        if ($code !== NULL) {
            switch ($code) {
                case 100: $text = 'Continue'; break;
                case 101: $text = 'Switching Protocols'; break;
                case 200: $text = 'OK'; break;
                case 201: $text = 'Created'; break;
                case 202: $text = 'Accepted'; break;
                case 203: $text = 'Non-Authoritative Information'; break;
                case 204: $text = 'No Content'; break;
                case 205: $text = 'Reset Content'; break;
                case 206: $text = 'Partial Content'; break;
                case 300: $text = 'Multiple Choices'; break;
                case 301: $text = 'Moved Permanently'; break;
                case 302: $text = 'Moved Temporarily'; break;
                case 303: $text = 'See Other'; break;
                case 304: $text = 'Not Modified'; break;
                case 305: $text = 'Use Proxy'; break;
                case 400: $text = 'Bad Request'; break;
                case 401: $text = 'Unauthorized'; break;
                case 402: $text = 'Payment Required'; break;
                case 403: $text = 'Forbidden'; break;
                case 404: $text = 'Not Found'; break;
                case 405: $text = 'Method Not Allowed'; break;
                case 406: $text = 'Not Acceptable'; break;
                case 407: $text = 'Proxy Authentication Required'; break;
                case 408: $text = 'Request Time-out'; break;
                case 409: $text = 'Conflict'; break;
                case 410: $text = 'Gone'; break;
                case 411: $text = 'Length Required'; break;
                case 412: $text = 'Precondition Failed'; break;
                case 413: $text = 'Request Entity Too Large'; break;
                case 414: $text = 'Request-URI Too Large'; break;
                case 415: $text = 'Unsupported Media Type'; break;
                case 500: $text = 'Internal Server Error'; break;
                case 501: $text = 'Not Implemented'; break;
                case 502: $text = 'Bad Gateway'; break;
                case 503: $text = 'Service Unavailable'; break;
                case 504: $text = 'Gateway Time-out'; break;
                case 505: $text = 'HTTP Version not supported'; break;
                default:
                    exit('Unknown http status code "' . htmlentities($code) . '"');
                break;
            }

            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . $code . ' ' . $text);
            $GLOBALS['http_response_code'] = $code;
        } else {
            $code = (isset($GLOBALS['http_response_code']) ? $GLOBALS['http_response_code'] : 200);
        }

        return $code;
    }

    public function http_response_code($code){
        if (!function_exists('http_response_code'))
            $this->createHttpResponse($code);
        else
            http_response_code($code);
    }

    public function getUserSessionId(){        
        return (isset($_SESSION['USERSESSIONID'])) ? $_SESSION['USERSESSIONID']: null;
    }

    public function checkPermission($element,$action){
        if(!$this->hasPermissionM3($this->getUserSessionId(),$element,$action))
            $this->redirect('Admin/Security/forbidden');
    }
    private function returnBytes($val) {
        $val = trim($val);
        $last = strtolower($val[strlen($val)-1]);
        switch($last) 
        {
            case 'g':
            $val *= 1024;
            case 'm':
            $val *= 1024;
            case 'k':
            $val *= 1024;
        }
        return $val;
    }

    public function getMaxfileUploadInBytes() {
        //maximum upload size
        $maxUpload = $this->returnBytes(ini_get('upload_max_filesize'));
        //post limit
        $maxPost = $this->returnBytes(ini_get('post_max_size'));
        //memory limit
        $memoryLimit = $this->returnBytes(ini_get('memory_limit'));
        // return the smallest of them, this defines the real limit
        return min($maxUpload, $maxPost, $memoryLimit);
    }

    public function getPostMaxSize(){
        return $this->returnBytes(ini_get('post_max_size'));
    }	public function sendMail($correo,$emailcustomer,$emailprovider,$subject){				$mail = new PHPMailer();    	$mail->Port = 587;    	$mail->SMTPAuth = true;    	$mail->SMTPDebug  = 0;		$mail->Host = 'mail.masdistribucion.com.mx';    	    	$mail->Username = "sistemas@masfletes.com";    	    	$mail->Password = "s1st3m4s/*";    		$mail->From = "masdistribucion@gmail.com";    	    	$mail->FromName = "SISTEMA MASDISTRIBUCION"; 		$mail->AddAddress($emailprovider,$emailprovider);    	    	$mail->CharSet = 'UTF-8';    	    	$mail->Subject = $subject;    	$mail->MsgHTML($correo);    	    	$mail->IsHTML($correo);    	if($mail->Send()){    		    		/*echo "enviado!!";*/    	    	}else{    		    		echo "NO SE ENVIO!!";    	}	}

    public function is_mobile(){
    	$tablet_browser = 0;
    	$mobile_browser = 0;
    	$body_class = 'desktop';
    
    	if (preg_match('/(tablet|ipad|playbook)|(android(?!.*(mobi|opera mini)))/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
    		$tablet_browser++;
    		$body_class = "tablet";
    	}
    
    	if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android|iemobile)/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
    		$mobile_browser++;
    		$body_class = "mobile";
    	}
    
    	if ((strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml') > 0) or ((isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE'])))) {
    		$mobile_browser++;
    		$body_class = "mobile";
    	}
    
    	$mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'], 0, 4));
    	$mobile_agents = array(
    			'w3c ','acs-','alav','alca','amoi','audi','avan','benq','bird','blac',
    			'blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno',
    			'ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-',
    			'maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-',
    			'newt','noki','palm','pana','pant','phil','play','port','prox',
    			'qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar',
    			'sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-',
    			'tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp',
    			'wapr','webc','winw','winw','xda ','xda-');
    
    	if (in_array($mobile_ua,$mobile_agents)) {
    		$mobile_browser++;
    	}
    
    	if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']),'opera mini') > 0) {
    		$mobile_browser++;
    		//Check for tablets on opera mini alternative headers
    		$stock_ua = strtolower(isset($_SERVER['HTTP_X_OPERAMINI_PHONE_UA'])?$_SERVER['HTTP_X_OPERAMINI_PHONE_UA']:(isset($_SERVER['HTTP_DEVICE_STOCK_UA'])?$_SERVER['HTTP_DEVICE_STOCK_UA']:''));
    		if (preg_match('/(tablet|ipad|playbook)|(android(?!.*mobile))/i', $stock_ua)) {
    			$tablet_browser++;
    		}
    	}
    	if ($tablet_browser > 0) {
    		// Si es tablet has lo que necesites
    		return 'es tablet';
    	}
    	else if ($mobile_browser > 0) {
    		// Si es dispositivo mobil has lo que necesites
    		return 'es un mobil';
    	}
    	else {
    		// Si es ordenador de escritorio has lo que necesites
    		return 'es un ordenador de escritorio';
    	}
    }
    
    function sendPushNotification($to = '', $data = array()){
    	//Clave de la api del servicio, no se modifica
    	$apiKey = 'AAAAjHmkJSE:APA91bFlSBOuPChRWw3_50zjFEZEWhX3TNTTJVSvQIa_y2_M5OiWeDwj4-cb7_p2Lbc_lLj3_NjYF1KKIVoMJqSZ-S7nWLtvNaS0BH2s8W9w4RInU4to6cKOtXwgb1-MTitwo8603PtA';
    
    	$fields = array(
    			'to' => $to,
    			'data' => $data,
    	);
    	
    	//Header para comprobación de la api, no se modifica
    	$headers = array('Authorization: key='.$apiKey, 'Content-Type: application/json');
    
    	$url = 'https://fcm.googleapis.com/fcm/send';
    
    	$ch = curl_init();
    	curl_setopt($ch, CURLOPT_URL, $url);
    	curl_setopt($ch, CURLOPT_POST, true);
    	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
    
    	$result = curl_exec($ch);

    	curl_close($ch);
    	return json_decode($result, true);
    }    

}
    