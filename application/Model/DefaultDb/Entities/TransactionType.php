<?php
/**
 * @Entity(repositoryClass="DefaultDb_Repositories_TransactionTypeRepository")
 * @Table(name="transaction_type")
 */
class DefaultDb_Entities_TransactionType
{
    /**
     * @Id @GeneratedValue @Column(type="integer", name="id")
     * @var integer
     */
    protected $id;
    
    /**
     * @Column(type="string", name="code", length=5, nullable=true)
     * @var string
     */
    protected $code;

    /**
     * @Column(type="string", name="name", length=30, nullable=true)
     * @var string
     */
    protected $name;
    
    public function getId(){
        return $this->id;
    }
    
    public function getName(){
        return $this->name;
    }
    
    public function getCode(){
        return $this->code;
    }
    
    public function setId($id){
        $this->id = $id;
    }
    
    public function setName($name){
        $this->name = $name;
    }
    
    public  function setCode($code){
        $this->code = $code;
    }
}