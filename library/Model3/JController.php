<?php
use com\masfletes\db\DBUtil;
/**
 * Clase que extiende Model3_Controller. 
 * Su propósito es manejar peticiones y respuestas en formato JSON
 */
class JController extends Model3_Controller
{    
    public $currentUserId;
  
    public function init()
    {
        $this->setResponseJSON(TRUE);        
        $this->currentUserId = Model3_Auth::getCredentials("id");        
    }
    
    public function setResponseJSON($param){
        if($param==true){
            header('Content-type: application/json; charset=utf-8');
            $this->responseJSON = TRUE;
        }else{
           header('Content-type: text/html;application/xhtml+xml;application/xml; charset=utf-8',true); 
           $this->responseJSON = FALSE;
        }
    }

    public function getArrayValue($key,$arr){
        if($arr==null||!is_array($arr)) return null;
        return array_key_exists($key,$arr)? $arr[$key] : null;
    }

    public function logAndResolveException($ex,$params){    

        $errMsg = $ex->getMessage();        
        $conn = $this->getConnection();
        $requestUrl = $this->getRequest()->getRequestUri(); 
        $params = array('sessionId'=>$this->getUserSessionId()) + $params;
        
        $errorCode = null;
        if($ex->getCode()==5000){
            $errorCode = $ex->getCode();
        } else if ($ex->getCode() == 1457) {
            $errorCode = $ex->getCode();
        }elseif (property_exists($ex, "errorInfo")) {
            $errorCode = $ex->errorInfo[1];
        }

        DBUtil::addLog($conn,$requestUrl, $params,"code:".$errorCode."  msg:".$errMsg);

        switch ($errorCode) {
            case 1457:
                echo json_encode(array("error" => $errMsg));
                break;
            case 1048:
                echo json_encode(array("error" => "Algun campo proporcionado no puede quedar en blanco, ya que es requerido"));
                break;
            case 1451:
                echo json_encode(array("error" => "Existen registros relacionados. No es posible eliminar el registro actual"));
                break;
            case 50000:
                $infoError = 'EXCEPCION_USUARIO:';
                $posError = strpos($errMsg, $infoError);
                if($posError>0){
                    $errMsg = substr($errMsg, $posError+strlen($infoError)+1);
                    echo json_encode(array("error" => $errMsg, "code" => $errorCode)); 
                }
                else
                    $this->createResponse500();
                break;            
            default:                
                $this->createResponse500();
                break;
        }
        exit;
    }

    public function createResponse500(){
        $this->createHttpResponse(500);
        exit;
    }

    public function createResponse401(){
        $this->createHttpResponse(401);
        exit;
    }

    public function createResponse403(){
        $this->createHttpResponse(403);
        exit;
    }

    public function generateUserException($message){
        throw new Exception($message, 5000);
    }

    public function sendUserMessage($message){
        echo json_encode(array(
                        "success"=>true
                        ,"message"=>$message
                        ));
        exit;
    }  
}