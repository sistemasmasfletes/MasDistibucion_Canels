<?php
/**
 * @Entity(repositoryClass="DefaultDb_Repositories_LogRepository")
 * @Table(name="log")
 */
class DefaultDb_Entities_Log{
    /**
    * @Id @GeneratedValue @Column(type="integer")
     * @var integer
     */
    protected $id;

    /**
     * @Column(type="datetime", nullable=true)
     * @var datetime
     */
    protected $date;

    /**
     * @Column(type="string", length=150, nullable=true)
     * @var datetime
     */
    protected $action;

    /**
     * @Column(type="string", length=500, nullable=true)
     * @var datetime
     */
    protected $params;

    /**
     * @Column(type="string", length=350), nullable=true
     * @var datetime
     */
    protected $error;

    
    public function setId($id){
        $this->id=$id;
    }
    
    public function setDate($date){
        $this->date = $date;
    }

    public function setAction($action){
        $this->action = $action;
    }

    public function setParams($params){
        $this->params = $params;
    }

    public function setError($error){
        $this->error = $error;
    }

    public function getId(){
        return $this->id;
    }

    public function getDate(){
        return $this->date;
    }

    public function getAction(){
        return $this->action;
    }

    public function getUrl(){
        return $this->params;
    }
    
    public function getError(){
        return $this->error;
    }    
}