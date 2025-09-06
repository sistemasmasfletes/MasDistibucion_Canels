<?php



/*

 * To change this license header, choose License Headers in Project Properties.

 * To change this template file, choose Tools | Templates

 * and open the template in the editor.

 */



/**

 *  DB - A simple database class

 *

 * @author		Author: Vivek Wicky Aswal. (https://twitter.com/#!/VivekWickyAswal)

 * @git 		https://github.com/indieteq/PHP-MySQL-PDO-Database-Class

 * @version      0.2ab

 *

 */

// se elimin esta llamada para no guardar el log

//require("Log.class.php");



class DB {

    # @object, The PDO object



    private $pdo;



    # @object, PDO statement object

    private $sQuery;



    # @array,  The database settings

    private $settings;



    # @bool ,  Connected to the database

    private $bConnected = false;



    # @object, Object for logging exceptions

    private $log;



    # @array, The parameters of the SQL query

    private $parameters;



    public $error;



    /**

     *   Default Constructor

     *

     * 	1. Instantiate Log class.

     * 	2. Connect to database.

     * 	3. Creates the parameter array.

     */

    public function __construct() {

        //$this->log = new Log();

        //echo ' --- Db.class.php -> antes de conectarse ';

        $this->Connect();

        //echo ' --- Db.class.php -> despues de conectarse ';

        $this->parameters = array();

    }



    /**

     * 	This method makes connection to the database.

     *

     * 	1. Reads the database settings from a ini file.

     * 	2. Puts  the ini content into the settings array.

     * 	3. Tries to connect to the database.

     * 	4. If connection failed, exception is displayed and a log file gets created.

     */

    private function Connect() {

        $this->settings = parse_ini_file("settings.ini.php");

        //echo ' --- Db.class.php -> despues de leer el archivo settings.ini.php <br>';

        //echo $this->settings["dbname"] . '<br>';





        $dsn = 'mysql:dbname=' . $this->settings["dbname"] . ';host=' . $this->settings["host"] . '';

        //echo ' ---Db.class.php -> $dsn ' . $dsn . '<br>';

        try {

            # Read settings from INI file, set UTF8

            //echo ' ---Db.class.php -> $this->settings ' . $this->settings["user"] . '<br>';

            //echo ' ---Db.class.php -> $this->settings ' . $this->settings["password"] . '<br>';



            $this->pdo = new PDO($dsn, $this->settings["user"], $this->settings["password"], array(

                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"

            ));

            //echo ' ---Db.class.php -> paso la linea de crear el PDO  <br>';

            # We can now log any exceptions on Fatal error.

            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);



            # Disable emulation of prepared statements, use REAL prepared statements instead.

            $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);



            # Connection succeeded, set the boolean to true.

            $this->bConnected = true;

            //echo 'hola';

        } catch (PDOException $e) {

            echo ' ---Db.class.php -> marco error al conectarse <br> ';

            # Write into log

            echo $this->ExceptionLog($e->getMessage());

            die();

        }

    }



    /*

     *   You can use this little method if you want to close the PDO connection

     *

     */



    public function CloseConnection() {

        # Set the PDO object to null to close the connection

        # http://www.php.net/manual/en/pdo.connections.php

        $this->pdo = null;

    }



    /**

     * 	Every method which needs to execute a SQL query uses this method.

     *

     * 	1. If not connected, connect to the database.

     * 	2. Prepare Query.

     * 	3. Parameterize Query.

     * 	4. Execute Query.

     * 	5. On exception : Write Exception into the log + SQL query.

     * 	6. Reset the Parameters.

     */

    private function Init($query, $parameters = "") {

        # Connect to database

        if (!$this->bConnected) {

            $this->Connect();

        }

        try {

            # Prepare query

            $this->sQuery = $this->pdo->prepare($query);



            # Add parameters to the parameter array

            $this->bindMore($parameters);



            # Bind parameters

            if (!empty($this->parameters)) {

                foreach ($this->parameters as $param => $value) {



                    $type = PDO::PARAM_STR;

                    switch ($value[1]) {

                        case is_int($value[1]):

                            $type = PDO::PARAM_INT;

                            break;

                        case is_bool($value[1]):

                            $type = PDO::PARAM_BOOL;

                            break;

                        case is_null($value[1]):

                            $type = PDO::PARAM_NULL;

                            break;

                    }

                    // Add type when binding the values to the column

                    $this->sQuery->bindValue($value[0], $value[1], $type);

                }

            }



            # Execute SQL

            $this->sQuery->execute();

        } catch (PDOException $e) {

            # Write into log and display Exception

            //echo $this->ExceptionLog($e->getMessage(), $query);

            $this->error = $e->getMessage();

            return false;

            //die();

        }



        # Reset the parameters

        $this->parameters = array();

    }



    /**

     * 	@void

     *

     * 	Add the parameter to the parameter array

     * 	@param string $para

     * 	@param string $value

     */

    public function bind($para, $value) {

        $this->parameters[sizeof($this->parameters)] = [":" . $para, $value];

    }



    /**

     * 	@void

     *

     * 	Add more parameters to the parameter array

     * 	@param array $parray

     */

    public function bindMore($parray) {

        if (empty($this->parameters) && is_array($parray)) {

            $columns = array_keys($parray);

            foreach ($columns as $i => &$column) {

                $this->bind($column, $parray[$column]);

            }

        }

    }



    /**

     *  If the SQL query  contains a SELECT or SHOW statement it returns an array containing all of the result set row

     * 	If the SQL statement is a DELETE, INSERT, or UPDATE statement it returns the number of affected rows

     *

     *   	@param  string $query

     * 	@param  array  $params

     * 	@param  int    $fetchmode

     * 	@return mixed

     */

    public function query($query, $params = null, $fetchmode = PDO::FETCH_ASSOC) {

        $query = trim(str_replace("\r", " ", $query));



        $this->Init($query, $params);



        $rawStatement = explode(" ", preg_replace("/\s+|\t+|\n+/", " ", $query));



        # Which SQL statement is used

        $statement = strtolower($rawStatement[0]);



        if ($statement === 'select' || $statement === 'show') {

            return $this->sQuery->fetchAll($fetchmode);

        } elseif ($statement === 'insert' || $statement === 'update' || $statement === 'delete') {

            return $this->sQuery->rowCount();

        } else {

            return NULL;

        }

    }



    /**

     *  Returns the last inserted id.

     *  @return string

     */

    public function lastInsertId() {

        return $this->pdo->lastInsertId();

    }



    /**

     * Starts the transaction

     * @return boolean, true on success or false on failure

     */

    public function beginTransaction() {

        return $this->pdo->beginTransaction();

    }



    /**

     *  Execute Transaction

     *  @return boolean, true on success or false on failure

     */

    public function executeTransaction() {

        return $this->pdo->commit();

    }



    /**

     *  Rollback of Transaction

     *  @return boolean, true on success or false on failure

     */

    public function rollBack() {

        return $this->pdo->rollBack();

    }



    /**

     * 	Returns an array which represents a column from the result set

     *

     * 	@param  string $query

     * 	@param  array  $params

     * 	@return array

     */

    public function column($query, $params = null) {

        $this->Init($query, $params);

        $Columns = $this->sQuery->fetchAll(PDO::FETCH_NUM);



        $column = null;



        foreach ($Columns as $cells) {

            $column[] = $cells[0];

        }



        return $column;

    }



    /**

     * 	Returns an array which represents a row from the result set

     *

     * 	@param  string $query

     * 	@param  array  $params

     *   	@param  int    $fetchmode

     * 	@return array

     */

    public function row($query, $params = null, $fetchmode = PDO::FETCH_ASSOC) {

        $this->Init($query, $params);

        $result = $this->sQuery->fetch($fetchmode);

        $this->sQuery->closeCursor(); // Frees up the connection to the server so that other SQL statements may be issued,

        return $result;

    }



    /**

     * 	Returns the value of one single field/column

     *

     * 	@param  string $query

     * 	@param  array  $params

     * 	@return string

     */

    public function single($query, $params = null) {

        $this->Init($query, $params);

        $result = $this->sQuery->fetchColumn();

        $this->sQuery->closeCursor(); // Frees up the connection to the server so that other SQL statements may be issued

        return $result;

    }



    /**

     * Writes the log and returns the exception

     *

     * @param  string $message

     * @param  string $sql

     * @return string

     */

    private function ExceptionLog($message, $sql = "") {

        $exception = 'Unhandled Exception. <br />';

        $exception .= $message;

        $exception .= "<br /> You can find the error back in the log.";



        if (!empty($sql)) {

            # Add the Raw SQL to the Log

            $message .= "\r\nRaw SQL : " . $sql;

        }

        # Write into log

        //$this->log->write($message);



        return $exception;

    }

    // metodos implementados 
	
	public function operation_comments($idop){
		$comments =  $this->query("
				SELECT 
					cm.comment, usr.names, ust.name
				FROM comment cm
				left join users usr on cm.id_user = usr.id_users
                left join user_types ust on ust.id_user_types = usr.user_type
				where 
					cm.id_operation = :idop
				order by cm.id_comment DESC
				", array("idop" => $idop)
		);
		return $comments;
	}
	
	public function provider_operations($iduser){
		$operations =  $this->query("
				SELECT 
					top.id_transport_operation, top.operation_date, top.quantity, top.comments, top.description , top.documentation, top.applicant, top.status,
					lb.folio,
					cust.business_name,
					opd.rrt,opd.rrd,
					opd.receiver,opd.edt,opd.eta,opd.rtd,opd.rta,opd.rdd,opd.rda,opd.ert,opd.erd,opd.ete,opd.ede,opd.rte,opd.rde
				FROM transport_operations top
				left join relationship rl on rl.id_relationship = top.id_relationship
				left join companies cust on cust.id_companies = rl.id_company1
				left join letter_bearing lb on top.id_note =  lb.id_letter_bearing
				left join transportoperation_details opd on opd.id_operation = top.id_transport_operation
				where 
					top.id_user = :iduser
				order by top.id_transport_operation DESC limit 100
		", array("iduser" => $iduser)
		);
		return $operations;
	}

	public function customer_operations($idcompany){
		$operations =  $this->query("
				SELECT 
					top.id_transport_operation, top.operation_date, top.quantity, top.comments, top.description , top.documentation, top.applicant, top.status,
					lb.folio,
					prov.business_name,
					opd.rrt,opd.rrd,
					opd.receiver,opd.edt,opd.eta,opd.rtd,opd.rta,opd.rdd,opd.rda,opd.ert,opd.erd,opd.ete,opd.ede,opd.rte,opd.rde
				FROM transport_operations top
				left join relationship rl on rl.id_relationship = top.id_relationship
				left join companies prov on prov.id_companies = rl.id_company2
				left join letter_bearing lb on top.id_note =  lb.id_letter_bearing
				left join transportoperation_details opd on opd.id_operation = top.id_transport_operation
				where 
					rl.relationship_type = 1 and rl.id_company1 = :idcompany
				order by top.id_transport_operation DESC limit 100
		", array("idcompany" => $idcompany)
		);
		return $operations;
	}
  	
	public function driver_operations($iduser,$iteration){
	
		$and = ($iteration)?"and status not in('2','0','9','10','11','12','13')":"";

		$operations =  $this->query("
				select 
					op.id_transport_operation,op.operation_date,op.description,op.status,
					opd.rrt,opd.rrd,
					opd.receiver,opd.edt,opd.eta,opd.rtd,opd.rta,opd.rdd,opd.rda,opd.ert,opd.erd,opd.ete,opd.ede,opd.rte,opd.rde
				from 
					transportoperation_details opd
				left join drivers2 drdat on opd.id_driver = drdat.id_driver 
				left join transport_operations op on op.id_transport_operation = opd.id_operation
				where 
					drdat.id_user = :iduser  ".$and."
				order by 	op.id_transport_operation desc limit 25
				",
				array("iduser" => $iduser)
		);
	
		return $operations;
	
	}
	
	public function operation_location($idop) {
	
		$locations = $this->query("
					select 
					org.description ordescription,org.url_maps orgurl, concat_ws(' ',org.street,org.suburb,org.num) d1,
					dest.description destdescription,dest.url_maps desturl, concat_ws(' ',dest.street,dest.suburb,dest.num) d2
					from masdistr_masfletes_mf.transportoperation_details td
					left join masdistr_masfletes_mf.locations org on org.id_locations = td.id_source
					left join masdistr_masfletes_mf.locations dest on dest.id_locations = td.id_destination 
					where id_operation = :idop
				",
				array("idop" => $idop)
		);
	
		return $locations;
	}

	public function login($user,$pass) {
	
		$password = md5($pass);
		
		$login = $this->query("
						SELECT * FROM users 
				WHERE username = '$user' AND password = '$password'");
		return $login;
	}	
	
	public function tokenup($token,$iduser) {
	
		$updatetoken = $this->query("
				update users set token = '$token' 
				WHERE id = '$iduser'");
		return $updatetoken;
	}	
	
	////NO USADOS//////////
	/*public function log($user, $pass) {

        $usuario = $this->query("SELECT * FROM users  WHERE username = :username AND password = :password", array("username" => $user, "password" => md5($pass)));

        if (count($usuario) != 0)

            return true;

        else

            return false;

    }

    public function new_user($username,$password,$first_name,$last_name,$e_mail,$telephone,$job_position,$iduser_type,$last_namem,$ext,$idboss) {

    	$insert = $this->query("INSERT INTO users(username,password,first_name,last_name,e_mail,telephone,job_position,date,iduser_type,last_namem,tel_ext,idboss)

    							VALUES(:username,:password,:first_name,:last_name,:e_mail,:telephone,:job_position,:date,:iduser_type,:last_namem,:ext,:idboss)",

    			array("username"=>$username,"password"=>md5($password),"first_name"=>$first_name,"last_name"=>$last_name,

    					"e_mail"=>$e_mail,"telephone"=>$telephone,"job_position"=>$job_position,"date"=>date('Y-m-d'),

    					"iduser_type"=>$iduser_type,"last_namem"=>$last_namem,"ext"=>$ext,"idboss"=>$idboss));

    	if (count($insert) != 0){

    		return $insert;

    	}else{

    		return false;

    	}

    }*/	
	
	
}
