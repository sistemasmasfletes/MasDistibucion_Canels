<?php
/**
 * @Entity(repositoryClass="DefaultDb_Repositories_TipoDebitoRepository")
 * @Table(name="tbltipodebito")
 * @HasLifecycleCallbacks
 */
class DefaultDb_Entities_TipoDebito{

    /**
     * @Id @GeneratedValue @Column(type="integer")
     * @var integer
     */
    protected $id;
 
     /**
     * @tipoConcepto @Column(type="string", name="chrTipoDebito")
     * @var string  
     * */
    protected $tipoDebito;
   
    /**
     * @Column(type="datetime", name="dtdTimeStamp")
     * @var datetime
     */
    protected $timestamp;
    
     /** @PrePersist */
    public function doStuffOnPrePersist() {
        $this->setTimestamp( new DateTime() );
    }
    
    public function getId() {
        return $this->id;
    }

    public function getTipoDebito() {
        return $this->tipoDebito;
    }

    public function getTimestamp() {
        return $this->timestamp;
    }
    
    public function setId($id) {
        $this->id = $id;
    }

    public function setTipoDebito($tipoDebito) {
        $this->tipoDebito = $tipoDebito;
    }
    
    public function setTimestamp($timestamp) {
        $this->timestamp = $timestamp;
    }

}
