<?php
/**
 * @Entity(repositoryClass="DefaultDb_Repositories_PaisesRepository")
 * @Table(name="tblpaises", uniqueConstraints={@UniqueConstraint(name="search_idx", columns={"chrNombre"})})
 * @HasLifecycleCallbacks
 */
class DefaultDb_Entities_Paises{
	 /**
     * @Id @GeneratedValue @Column(type="integer")
     * @var integer
     */
    protected $id;
    
     /**
     * @Column(type="string", name="chrNombre", length=250, nullable=true)
     * @var string
     */
    protected $nombre;
    
    /**
     * @Column(type="string", name="comments", length=250, nullable=true)
     * @var string
     */
    protected $comments;
   
    /**
     * @Column(type="string", name="chrClient", length=250, nullable=true)
     * @var string
     */
    protected $client;

    /**
     * @Column(type="datetime", name="dtdTimeStamp")
     * @var datetime
     */
    protected $timestamp;
    
    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_User")
     * @JoinColumn(referencedColumnName="id", nullable=true)
     */
    protected $controller;
    
    /**
     * @Column(type="string", name="chrEstado", nullable=true)
     * @var string
    */
    private $estado;
    
     /** @PrePersist */
    public function doStuffOnPrePersist() {
        $this->setTimestamp( new DateTime() );
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    
    public function getNombre()
    {
        return $this->nombre;
    }
    
    public function getComments(){
        return $this->comments;
    }

    public function getClient() {
        return $this->client;
    }
    
    public function getTimestamp() {
        return $this->timestamp;
    }
    
    public function getController(){
        return $this->controller;
    }
    
    public function getEstado() {
        return $this->estado;
    }
    
    public function setId($id)
    {
        $this->id = $id;
    }

    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }
    
    public function setComments($comments){
        $this->comments = $comments;
    }

    public function setClient($client) {
        $this->client = $client;
    }
    public function setTimestamp($timestamp) {
        $this->timestamp = $timestamp;
    }
    
    public function setController($controller){
        $this->controller = $controller;
    }
    public function setEstado($estado) {
        $this->estado = $estado;
    }
  
}