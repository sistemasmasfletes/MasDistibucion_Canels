<?php
/**
 * @Entity
 * @Table(name="element_action")
 */
class DefaultDb_Entities_ElementAction{
    /**
    * @Id @GeneratedValue @Column(type="integer")
     * @var integer
     */
    protected $id;

    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_Element")
     */
    protected $element;

    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_Action")
     */
    protected $action;

    public function setElement($element){
        $this->element=$element;
    }

    public function getElement(){
        return $this->element;
    }

    public function setAction($action){
        $this->action=$action;
    }

    public function getAction(){
        return $this->action;
    }

    public function getId(){
        return $this->id;
    }
    
}