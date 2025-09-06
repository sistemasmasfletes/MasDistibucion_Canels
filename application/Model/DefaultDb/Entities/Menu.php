<?php
/**
 * @Entity(repositoryClass="DefaultDb_Repositories_MenuRepository")
 * @Table(name="menu")
 */
class DefaultDb_Entities_Menu{
    /**
    * @Id @GeneratedValue @Column(type="integer")
     * @var integer
     */
    protected $id;

     /**
     * @Column(type="string", length=50)
     * @var string
     */
    protected $title;

     /**
     * @Column(type="string", length=50, nullable=true)
     * @var string
     */
    protected $url;

    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_Menu")
     * @JoinColumn(nullable=true, name="parent_id")
     */
    protected $parent;

    /**
     * @Column(type="integer", nullable=true)
     * @var string
     */
    protected $position;

    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_ElementAction")
     * @JoinColumn(nullable=true, name="element_action_id")
     */
    protected $elementAction;

    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_Role")
     * @JoinColumn(nullable=true, name="role_id")
     */
    protected $role;

    public function setId($id){
        $this->id=$id;
    }
    
    public function setTitle($title){
        $this->title = $title;
    }

    public function setUrl($url){
        $this->url = $url;
    }

    public function setParent($parent){
        $this->parent = $parent;
    }

    public function setPosition($position){
        $this->position = $position;
    }

    public function setElementAction($elementAction){
        $this->elementAction=$elementAction;
    }

    public function setRole($role){
        $this->role=$role;
    }

    public function getId(){
        return $this->id;
    }

    public function getTitle(){
        return $this->title;
    }

    public function getUrl(){
        return $this->url;
    }

    public function getParent(){
        return $this->parent;
    }

    public function getPosition(){
        return $this->position;
    }

    public function getElementAction(){
        return $this->elementAction;
    }

    public function getRole(){
        return $this->role;
    }    
}