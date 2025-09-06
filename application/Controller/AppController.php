<?php
class AppController extends JController{

	public function init()
    {
    	parent::init();
    	if (!Model3_Auth::isAuth()) {
            $this->redirect('Index/index');
        }

        $this->userSessionId = $_SESSION['USERSESSIONID'];

    }

    public function indexAction()
    {
        $this->setResponseJSON(FALSE);
        $this->view->setTemplate('Application');
        if($this->getRequest()->getPost()){
        	$post = $this->getRequest()->getPost();
        	
        	echo "<script> var idbranche = '".$post['idbranche']."' </script>";
        	/*$this->view->getJsManager()->addJsVar('idbranche', '\''.$this->view->getBaseUrl().'/Ajax/Users/typeClient'.'\'');*/
     		/*$this->view->getJsManager()->addJsVar('idbranche', '\''.$post['idbranche'].'\'');*/
        }else{
        	$this->view->getJsManager()->addJsVar('idbranche', '\''."".'\'');
        }
    }

    public function getSesionDataAction(){    	
    	 echo json_encode(array("session"=>$this->userSessionId,"userData"=>Model3_Auth::getCredentials()));
    }

    public function test_phpAction(){
        $uploaddir = '../public/images/evidencias/';
        $nombre =$_POST['nombre'];
        echo $nombre;
        $ext='.png';
        $filedir= $uploaddir.$nombre.$ext;
        $imagen = base64_decode($_POST['action']);
       	if(file_put_contents($filedir,$imagen)){

        /*****Funciones optimizar imagenes*******/
        
        //Parametros optimizacion, resoluci�n m�xima permitida
        $max_ancho = 1024;
        $max_alto = 780;
        
        	//Redimensionar
        	$rtOriginal=$filedir;
        
       		$original = imagecreatefrompng($rtOriginal);
        
        	list($ancho,$alto)=getimagesize($rtOriginal);
        
        	$x_ratio = $max_ancho / $ancho;
        	$y_ratio = $max_alto / $alto;
        
        	if( ($ancho <= $max_ancho) && ($alto <= $max_alto) ){
        		$ancho_final = $ancho;
        		$alto_final = $alto;
        	}elseif (($x_ratio * $alto) < $max_alto){
        		$alto_final = ceil($x_ratio * $alto);
        		$ancho_final = $max_ancho;
        	}else{
        		$ancho_final = ceil($y_ratio * $ancho);
        		$alto_final = $max_alto;
        	}
        
        	$lienzo=imagecreatetruecolor($ancho_final,$alto_final);
        
        	imagecopyresampled($lienzo,$original,0,0,0,0,$ancho_final, $alto_final,$ancho,$alto);
        	//imagepng($lienzo,$filedir);
        	$res =	imagejpeg($lienzo,$filedir, 75);
       	}
       	/*****Terminan Funciones optimizar imagenes*******/
 
        echo $imagen;   
    }
    
}