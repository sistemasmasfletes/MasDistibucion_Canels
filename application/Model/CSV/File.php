<?php

define('UTF32_BIG_ENDIAN_BOM', chr(0x00).chr(0x00).chr(0xFE).chr(0xFF));
define('UTF32_LITTLE_ENDIAN_BOM', chr(0xFF).chr(0xFE).chr(0x00).chr(0x00));
define('UTF16_BIG_ENDIAN_BOM', chr(0xFE).chr(0xFF));
define('UTF16_LITTLE_ENDIAN_BOM', chr(0xFF).chr(0xFE));
define('UTF8_BOM', chr(0xEF).chr(0xBB).chr(0xBF));

class CSV_File
{
    private $_fileContent = false;
    private $_lines = false;
    private $_headers = false;
    private $_requiredHeaders = false;
    private $_users = false;
    private $_usersVerified = false;
    private $_messages = false;

    const UTF8 = 'UTF-8';
    const UTF32BE = 'UTF-32BE';
    const UTF32LE = 'UTF-32LE';
    const UTF16BE = 'UTF-16BE';
    const UTF16LE = 'UTF-16LE';

    const TYPE_STRING = 'string';
    const TYPE_DOUBLE = 'double';
    const TYPE_EMAIL = 'email';

    /**
     * Funcion que realiza la conversion de datos en un archivo csv a registros en base de datos
     * @param string $fileName  nombre del archivo
     */
    public function __construct($fileName)
    {
        if(empty($fileName) === true)
        {
            throw new Exception('No file specified');
        }
        $this->_fileContent = file_get_contents($fileName);
        if($this->_fileContent === false)
        {
            throw new Exception('The file cannot be read');
        }
        $this->_fileContent = $this->convertEncoding($this->_fileContent);
    }

    public function parse()
    {
        // obtenemos las lineas en el archivo a cargar incluyendo los encabezados
        $this->getLines();
        $this->parseHeader();
        $this->getUsers();
    }

    
    public function verfifyData()
    {
        $result = array('ok' => 0, 'error' => 0, 'all' => 0);
        $this->_usersVerified = array();
        $this->_messages = array();
        
        foreach($this->_users as $key => $user)
        {
            $result['all']++;
            $repeat = false;
            try
            {
                $this->verifyUserValues($user);
                $result['ok']++;
            }
            catch(Exception $e)
            {
                $result['error']++;
                $this->_messages[] = 'The user No. '.($key + 1).' has invalid values .'.$e->getMessage();                
            }
        }
        return $result;
    }

    /**
     * Verifica que un producto a importar tenga los campos requeridos con el tipo
     * de valor correcto.
     * @param stdClass $product
     */
    private function verifyUserValues(DefaultDb_Entities_User $user)
    {
        /**
         * Arreglo con los campos que son requeridos y el tipo que deben de tener.
         */
        $requiredValues = array('username' => self::TYPE_EMAIL,);
        foreach($requiredValues as $name => $type)
        {
            $valueToVerify = null;
            
            switch ($name)
            {
                case 'username' : $valueToVerify = $user->getUsername();
                    break;
            }
            
            $validations = $this->validValue($valueToVerify, $type, $name);
            
            if( $validations )
            { 
                //revisamos que el usuario no exista
                if( $this->existUserName($user) )
                    $this->_usersVerified[] = $user;
            }
        }
    }

    public function save()
    {
        $db = Model3_Registry::getInstance()->get('databases');
        $entityManager = $db['DefaultDb'];
        /* @var $entityManager Doctrine\ORM\EntityManager */        
        foreach($this->_usersVerified as $user)
        {        
            $entityManager->persist($user);
            $entityManager->flush();
        }
    }

    /**
     * Verifica que un valor no sea una cadena vacia, o una de las cadenas no validas
     * ("null","false","no"). Si el valor corresponde a alguno de estos valores
     * entonces se retorna el valor por default.
     * @param mixed $value
     * @param mixed $valueDefault
     * @return mixed
     */
    private function checkValue($value, $valueDefault)
    {
        $value = strtolower($value);
        if(empty($value) === true ||
                strcmp($value, 'null') == 0 ||
                strcmp($value, 'false') == 0 ||
                strcmp($value, 'no') == 0)
        {
            $value = $valueDefault;
        }
        return $value;
    }

    /**
     * Valida un valor que recibe como parametro.<br/>
     * La validacion es de acuerdo al segundo parametro $type<br/>
     * STRING Se valida como cadena se valida que la cadena no vaya vacia.<br/>
     * DOUBLE Se valida que la expresion $value sea un numero ya sea entero o flotante.
     * @param mixed $value
     * @param integer $type
     */
    private function validValue($value, $type, $name)
    {
        switch($type)
        {
            case self::TYPE_STRING:
                $ret = ( empty($value) === false );
                break;
            case self::TYPE_DOUBLE:
                $ret = is_numeric($value) === true;
                break;
            
            case self::TYPE_EMAIL :
                $validator = new Model3_Validator_Form();
                $ret = $validator->validateEmail($value);
                break;
        }
        
        if($ret === false)
        {
            throw new Exception('The value "'.$value.'" is invalid for "'.$name.'"');
        }
        
        return $ret;
    }

    /**
     * Obtiene el nombre de los campos requeridos y verifica que existan los campos
     * requeridos.
     * Lanza una excepcion si alguno de los campos requeridos no existe en el archivo
     * de importacion.
     */
    private function parseHeader()
    {
        $this->_requiredHeaders = array(
            'first name',
            'last name',
            'email'/*,
            'sku'*/
            );
        $this->getHeaders();
        $this->verifyRequieredHeaders();
    }

    /**
     * Obtenemos los nombres de las columnas.
     */
    private function getHeaders()
    {
        $this->_headers = explode(',', $this->_lines[0]);
        unset($this->_lines[0]);
    }

    private function verifyRequieredHeaders()
    {
        foreach($this->_requiredHeaders as $requiered)
        {
            if(in_array($requiered, $this->_headers) === false)
            {
                throw new Exception('Column missing : "'.$requiered.'".');
            }
        }
    }

    /**
     * Crea un arreglo de objetos Defaul donde el nombre de cada columna es
     * una propiedad del objeto y su valor es el valor correspondiente a la
     * interseccion de la fila y la columna analizada.
     */
    private function getUsers()
    {
        $this->_users = array();
        foreach($this->_lines as $line)
        {

            $values = explode(',', $line);
            $obj = new DefaultDb_Entities_User();
            foreach($this->_headers as $key => $header)
            {
                $value = htmlentities(trim($values[$key]));
                
                switch($header)
                {
                    case 'first name': $obj->setFirstName($value);                        
                        break;
                    case 'last name' : $obj->setLastName($value);
                        break;
                    case 'email' : $obj->setUsername($value);
                        break;
                    case 'company' : $obj->setCompany($value);
                        break;
                    case 'website' : $obj->setCompanyUrl($value);
                        break;
                    case 'country' : $obj->setCountry($value);
                        break;
                }
                
                $value = isset($value) === true ? trim($value) : null;               
            }
            $obj->setTitle('');
            $obj->setLocalNumber('');
            $obj->setCellPhone('');
            $obj->setLinkFacebook('');
            $obj->setLinkLinkedIn('');
            $obj->setLinkMyspace('');
            $obj->setTypeUser(0);
            $obj->setHeadShot('');
            $obj->setDescription('');
            $obj->setDocument('');
            $obj->setDocumentDescription('');
            $obj->setAllowRequests(1);            
            $obj->setUseBreak(0);            
            $obj->setMeetingDuration('60');
            $scheduleStart = new DateTime();
            $scheduleStart->setTime(8, 0);
            $scheduleEnd = new DateTime();
            $scheduleEnd->setTime(20, 0);
            $breakStart = new DateTime();
            $breakStart->setTime(14, 0);
            $breakEnd = new DateTime();
            $breakEnd->setTime(15, 0);

            $obj->setScheduleStart($scheduleStart);
            $obj->setScheduleEnd($scheduleEnd);
            $obj->setBreakStart($breakStart);
            $obj->setBreakEnd($breakEnd);
            $obj->setTypeLoginUser(DefaultDb_Entities_User::USER_REGISTER);
            //damos un password arbitrario            
            $obj->setPassword(md5('dealbusinesscenter01'));
            $this->_users[] = $obj;            
        }
    }

    /**
     * Obtenemos un arreglo de lineas del contenido del archivo.<br/>
     * Quita las comilla que encierran a los campos de tipo texto-
     * @param <type> $fileContent
     */
    private function getLines()
    {
        $cadenaAux = '';
        $fileContent = $this->_fileContent;
        $this->_lines = array();
        for($i = 0, $entreComillas = false; $i < strlen($fileContent); $i++)
        {
            $caracter = $fileContent{$i};
            if(!$entreComillas)
            {
                if($caracter == "\n")
                {
                    if($cadenaAux != '')
                    {
                        $this->_lines[] = $cadenaAux;
                        $cadenaAux = '';
                    }
                }
                elseif($caracter != "\r" && $caracter != '"')
                {
                    $cadenaAux .= $caracter;
                }
            }
            else
            {
                if(/*$caracter != ',' &&*/ $caracter != '"')
                {
                    if($caracter == ',')
                    {
                        $caracter = '|';
                    }
                    $cadenaAux .= $caracter;
                }
            }

            if($caracter == '"')
            {
                $entreComillas = !$entreComillas;
            }
        }
        if($cadenaAux != '')
        {
            $this->_lines[] = $cadenaAux;
            $cadenaAux = '';
        }        
    }

    private function convertEncoding($fileContent)
    {
        $encodingInternalName = mb_internal_encoding();
        if($encodingInternalName === false)
        {
            throw new Exception('No se detecto la codificacion');
        }
        $utfEncoding = $this->detectUtfEncoding($fileContent);
        if($utfEncoding !== FALSE)
        {
            if($utfEncoding != self::UTF8)
            {
                $fileContent = mb_convert_encoding($fileContent, $encodingInternalName, $utfEncoding);
            }
        }

        return $fileContent;
    }

    private function detectUtfEncoding($text)
    {
        $first2 = substr($text, 0, 2);
        $first3 = substr($text, 0, 3);
        $first4 = substr($text, 0, 3);
        $utfEncoding = false;

        if($first3 == UTF8_BOM)
        {
            $utfEncoding = self::UTF8;
        }
        elseif($first4 == UTF32_BIG_ENDIAN_BOM)
        {
            $utfEncoding = self::UTF32BE;
        }
        elseif($first4 == UTF32_LITTLE_ENDIAN_BOM)
        {
            $utfEncoding = self::UTF32LE;
        }
        elseif($first2 == UTF16_BIG_ENDIAN_BOM)
        {
            $utfEncoding = self::UTF16BE;
        }
        elseif($first2 == UTF16_LITTLE_ENDIAN_BOM)
        {
            $utfEncoding = self::UTF16LE;
        }
        return $utfEncoding;
    }

    public function getMessages()
    {
        return $this->_messages;
    }

    public function getUserssVerified()
    {
        return $this->_usersVerified;
    }

    /**
     *
     * @param DefaultDb_Entities_User $user 
     */
    private function existUserName(DefaultDb_Entities_User $user)
    {        
        if(property_exists($user, 'username') === true)
        {            
            $db = Model3_Registry::getInstance()->get('databases');
            $entityManager = $db['DefaultDb'];
            /* @var $entityManager Doctrine\ORM\EntityManager */
            $exist = $entityManager->getRepository('DefaultDb_Entities_User')->findOneBy(array('username'=>$user->getUsername()));
            $freeName = true;
            if ( $exist instanceof DefaultDb_Entities_User )
            {
                $freeName =  false;
            }

            if($freeName === false)
            {
                throw new Exception('<br/>Duplicated user '.$user->getUsername());                
            }
            
            return $freeName;
        }
    }

}