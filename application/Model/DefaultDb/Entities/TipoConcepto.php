<?php
/**
 * @Entity(repositoryClass="DefaultDb_Repositories_TipoConceptoRepository")
 * @Table(name="tbltipoconcepto")
 * @HasLifecycleCallbacks
 */
class DefaultDb_Entities_TipoConcepto{

    /**
     * @Id @GeneratedValue @Column(type="integer")
     * @var integer
     */
    protected $id;
 
     /**
     * @tipoConcepto @Column(type="string", name="chrTipoConcepto")
     * @var string  
     * */
    protected $tipoConcepto;
   
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

    public function getTipoConcepto() {
        return $this->tipoConcepto;
    }

    public function getTimestamp() {
        return $this->timestamp;
    }
    
    public function setId($id) {
        $this->id = $id;
    }

    public function setTipoConcepto($tipoConcepto) {
        $this->tipoConcepto = $tipoConcepto;
    }
    
    public function setTimestamp($timestamp) {
        $this->timestamp = $timestamp;
    }

}
