<?php

/**
 * @Entity(repositoryClass="DefaultDb_Repositories_CardOperatorsRepository")
 * @Table(name="tblcardoperators")
 */

class DefaultDb_Entities_CardOperators{
    /**
     * @Id @GeneratedValue @Column(type="integer")
     * @var integer
     */
    
    protected $id;
    
    /**
     * @Column(type="string", length=255, name="chrOperator")
     * @var string
     */
    protected $chrOperator;
    
    /**
     * @Column(type="string", length=11, name="chrUser")
     * @var string
     */
    protected $chrUser;
    
    
    // GETTERS
    public  function getId(){
        return $this->id;
    }
    
    public function getName(){
        return $this->chrOperator;
    }
    
    public function getUser(){
        return $this->chrUser;
    }
    
    // SETTERS
    public function setId($id){
        $this->id = $id;
    }
    
    public function setName($chrOperator){
        $this->chrOperator = $chrOperator;
    }
    
    public function setUser($chrUser){
        $this->chrUser = $chrUser;
    }
}