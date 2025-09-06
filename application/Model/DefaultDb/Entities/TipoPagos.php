<?php

/**
 * @Entity(repositoryClass="DefaultDb_Repositories_TipoPagosRepository")
 * @Table(name="tbltipopagos", uniqueConstraints={@UniqueConstraint(name="search_idx", columns={"chrtipoPago"})})
 * @HasLifecycleCallbacks
 */
class DefaultDb_Entities_TipoPagos {

    /**
     * @Id @GeneratedValue @Column(type="integer")
     * @var integer
     */
    protected $id;

    /**
     * @Column(type="string", length=250, name="chrTipoPago" )
     * @var string
     */
    protected $tipoPago;

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
    
     /** @PrePersist */
    public function doStuffOnPrePersist() {
        $this->setTimestamp( new DateTime() );
    }

    public function getId() {
        return $this->id;
    }

    public function getTipoPago() {
        return $this->tipoPago;
    }

    public function getClient() {
        return $this->client;
    }

    public function getTimestamp() {
        return $this->timestamp;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setTipoPago($tipoPago) {
        $this->tipoPago = $tipoPago;
    }

    public function setClient($client) {
        $this->client = $client;
    }

    public function setTimestamp($timestamp) {
        $this->timestamp = $timestamp;
    }

}
