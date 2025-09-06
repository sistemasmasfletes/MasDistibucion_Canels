<?php
namespace com\masfletes\db;
use \PDO;
use \PDOException;

/**
 * Clase de utilidad para manejar interacción entre PDO y Stored Procedures
 *
 * @author Andrés Hdz
 */
class DBUtil {
    
    public static function executeStoredProc($conn, $storedProcName, $storedProcParams, $fetchType = PDO::FETCH_NAMED){
        $statement = self::createStatementFromParameters($conn, $storedProcName, $storedProcParams);
        $statement->execute();
        $resultset = self::getResultsetFromStatement($statement, $fetchType);
        $statement->closeCursor();
        return $resultset;
    }
    

    //put your code here
    public static function getResultsetFromStatement($statement, $fetchType) {
        $originalResultset = array();
        $resultRows = array();
        $resultsets = array();
        do{
            try {
                $originalResultset = $statement->fetchAll($fetchType);
                //Revisar campos numéricos, ya que el array devuelto consta sólo de valores String.
                foreach ($originalResultset as $row) {
                    foreach ($row as $key => $value) {                        
                        if(is_numeric($value) && $key!='idsesion') 
                            $row[$key]=$value+0;
                        else
                            $row[$key]=/*utf8_encode(*/$value/*)*/;
                    }
                    $resultRows[]=$row;
                }                
                $resultsets[] = $resultRows;
                $resultRows=array();
            }catch(PDOException $pdoEx){
                if($pdoEx->getMessage()!='SQLSTATE[HY000]: General error')
                    throw $pdoEx;
            }
        }while ($statement->nextRowset());
        
        return $resultsets;
    }

       /**
        * Crea el Statement de la forma CALL sp_name(?,?,?)
        * Adicionalmente, setea el valor para cada parámetro
        */
    public static function createStatementFromParameters($conn, $storedProcName, $aParameters){
        $query = "CALL ".$storedProcName;
        $query .= "(";
        for($i=0;$i<count($aParameters);$i++){
            $query .= (($i==0) ? "" : ",")."?";
        }
        $query .= ")";
        $stmt = $conn->prepare($query);
        for($i=0;$i<count($aParameters);$i++){
            $currParam = $aParameters[$i];
            if($currParam==null)
                $stmt->bindValue($i+1,$currParam,PDO::PARAM_NULL);
            $stmt->bindValue($i+1,$currParam);
        }
        return $stmt;
    }
    
    /**
     * Crea el Statement de la forma CALL sp_name(:param_1,:param_2,:param_3)
     * Adicionalmente, setea el valor para cada parámetro
     */
    public static function createStatementFromParametersNamed($conn, $storedProcName, $aParameters){
        $query = "CALL ".$storedProcName;
        $aParams = array_keys($aParameters);
        $params = implode(",", $aParams);
        $query .= "(".$params.")";
        $stmt = $conn->prepare($query);
        foreach($aParameters as $key=>$value){
            if($value==null)
                $stmt->bindValue($key,$value,PDO::PARAM_NULL);
            $stmt->bindValue($key,$value);
        }
        return $stmt;
    }
    
    public static function getStoredProcParameters($conn,$procedureName){
        $stmt = $conn->prepare('SELECT param_list params FROM mysql.proc WHERE db=\'logistic_masfletes\' AND TYPE = \'PROCEDURE\' AND  NAME=:procName');
        $stmt->bindValue(':procName',$procedureName,PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $params = $result['params'];
        $aParams = explode(",", str_replace("\n"," ",$params));
        $arrayParams = array();
        for($i=0;$i<count($aParams);$i++){
            $currVal= strtolower(trim($aParams[$i]));
            if(strpos($currVal, 'in')>-1 )
                $currVal = substr($currVal, strlen ('in'));
            $aNameTypes = explode(" ",trim($currVal));
            $arrayParams[] = array(strtolower(trim($aNameTypes[0]))=>strtolower(trim($aNameTypes[1])));            
        }
        return $arrayParams;
    }
    
    public static function validarPermiso($conn,$idSesion,$pantalla,$accion){
        $aPermisos = array($idSesion,$pantalla,$accion);
        $result = self::executeStoredProc($conn, 'sp_permiso_validar', $aPermisos);
        $permisos = (int)$result[0][0]['permisos'];
        return $permisos>0;
    }


    public static function addLog(&$conn,$action,$params,$error){

        $strParams = "";

        foreach ($params as $key=>$param) {
            $strParams.= $key.'=';
            if(is_array($param)){
                $strParams.= "[";
                foreach ($param as $key1 => $value1) {
                    $strParams.= $key1.'='.$value1.'|';
                }
                $strParams.= "]";
                continue;
            }

            $strParams.=$param."|";
        }
        
        $dt = new \Datetime();
        $sql = "INSERT INTO log (date,action,params,error) values (?,?,?,?)";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(1,$dt->format('Y-m-d H:i:s'));
        $stmt->bindValue(2,$action);
        $stmt->bindValue(3,$strParams);
        $stmt->bindValue(4,$error);
        $stmt->execute();
        
        unset($stmt);
        unset($conn);

    }

    public static function generateUserException($message){
        throw new \Exception($message, 5000);
    }
    
    /**
     * Genera excepción para mostrar mensaje de error en pantalla.
     * @param type $message Mensaje de error a mostrar.
     * @throws \Exception Excepción creada.
     */
    public static function generateUserExceptionWithMessage($message){
        throw new \Exception($message, 1457);
    }
}
