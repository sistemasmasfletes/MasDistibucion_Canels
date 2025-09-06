<?php

/**
 * Clase Auth, esta clase auntentifica al usuario en el sistema.
 * @package Model3
 * @author Hector Benitez
 * @version 0.4
 * @copyright 2012 Hector Benitez
 */
use com\masfletes\db\DBUtil;
class Model3_Auth
{

    protected $_config;

    public function __construct()
    {
        $registry = Model3_Registry::getInstance();
        $config = $registry->get('config');
        $this->_config = $config->getArray();        
    }

    public function setTableData($cnx, $table, $usr = 'username', $psw = 'password')
    {
        unset($this->_config['user_data']);
        $this->_config['user_data'] = array('cnx' => $cnx, 'table' => $table, 'user' => $usr, 'pass' => $psw);
    }

    /**
     * Autentifica al usuario en el sistema por medio de su username y password
     * @param string $user El username del usuario
     * @param string $pass El password del usuario
     * @return bool|Regresa true si los datos del usuario son validos en la BD, en caso de fallar regresa false
     */
    public function authenticate($user, $pass)
    {
        $dbs = Model3_Registry::getInstance()->get('databases');
        $em = $dbs[$this->_config['user_data']['cnx']];
        /* @var $em Doctrine\ORM\EntityManager */
        
        $user = $em->getRepository($this->_config['user_data']['table'])->findOneBy(array($this->_config['user_data']['user'] => $user));
        if ($user)
        {
            $method = 'get'.ucwords($this->_config['user_data']['pass']);
            if ($user->$method() == $pass)
            {
                $_SESSION['__M3'][$this->_config['general']['app_name']]['Credentials'] = $user->getData();
                
                $resultsets = array();
                try{
                    $conn = $em->getConnection()->getWrappedConnection();
                    $userData = $user->getData();
                    $parameters=array($userData['id']);
                    $resultsets = DBUtil::executeStoredProc($conn, 'sp_sesion_crear', $parameters);
                    unset($conn);
                }catch(PDOException $ex){
                }
                $_SESSION['USERSESSIONID'] = $resultsets[0][0]['idsesion'];
                return true;
            }
            return false;
        }
    }

    /**
     * Obtiene la informacion del usuario auntentificado en el sistema
     * @param string $element la informacion especifica del usuario
     * @return $_SESSION['__M3']['Credentials'] o $_SESSION['__M3']['Credentials'][$element] | Si element es null regresa
     * toda la informacion del usuario  en caso contrario la que se le especifico en $element
     */
    public static function getCredentials($element = null)
    {
        $registry = Model3_Registry::getInstance();
        $configAux = $registry->get('config');
        $config = $configAux->getArray(); 
        
        if (!isset($_SESSION['__M3'][$config['general']['app_name']]))
            return null;
        if (!isset($_SESSION['__M3'][$config['general']['app_name']]['Credentials']))
            return null;
        if ($element == null)
            return $_SESSION['__M3'][$config['general']['app_name']]['Credentials'];
        else
            return $_SESSION['__M3'][$config['general']['app_name']]['Credentials'][$element];
    }

    /**
     * Borra toda la informacion del usuario en las variables de sesion
     */
    public static function deleteCredentials()
    {
        $registry = Model3_Registry::getInstance();
        $configAux = $registry->get('config');
        $config = $configAux->getArray(); 
        
        unset($_SESSION['__M3'][$config['general']['app_name']]['Credentials']);
        unset($_SESSION['USERSESSIONID']);
    }

    /**
     * Verifica si el usuario esta auntentificado en el sistema
     */
    public static function isAuth()
    {
        $registry = Model3_Registry::getInstance();
        $configAux = $registry->get('config');
        $config = $configAux->getArray(); 
        
        return isset($_SESSION['__M3'][$config['general']['app_name']]['Credentials']);
    }

    public static function refreshCredentials($user, $pass)
    {
        self::deleteCredentials();
        $auth = new Model3_Auth();
        return $auth->authenticate($user, $pass);
    }

}