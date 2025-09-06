<?php
/**
 * @Entity
 * @Table(name="action")
 */
class DefaultDb_Entities_Action{
	 /**
    * @Id @GeneratedValue @Column(type="integer")
     * @var integer
     */
    protected $id;

     /**
     * @Column(type="string", length=50)
     * @var string
     */
    protected $name;

    public function setName($name){
        $this->name=$name;
    }

    public function getName(){
        return $this->name;
    }

    public function getId(){
        return $this->id;
    }
}