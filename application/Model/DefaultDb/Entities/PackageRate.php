<?php

/**
 * @Entity(repositoryClass="DefaultDb_Repositories_PackageRateRepository")
 * @Table(name="package_rate")
 */
class DefaultDb_Entities_PackageRate
{
	/**
     * @Id @GeneratedValue @Column(type="integer")
     * @var integer
     */
    protected $id;

    /**
     * @Column(type="integer", name="element_id")
     * @var integer
     * Id de la entidad a la que hace referencia, puede ser id de ruta o id de punto de venta
     */
    protected $elementId;

    /**
     * @Column(type="smallint", name="element_type")
     * @var integer
     *
     * Tipo de elemento al que hace referencia la tarifa
     *===================================================
     * 1. Tarifa para Ruta
     * 2. Tarifa para Punto de Venta
     */

    protected $elementType;

    /**
     * @Column(type="datetime", name="date")
     * @var DateTime
     */
    protected $date;

    /**
     * @Column(type="decimal", precision=5, scale=2, name="client_rate")
     * @var float
     */
    protected $clientRate;

    /**
     * @Column(type="decimal", precision=5, scale=2, name="provider_fee")
     * @var float
     */
    protected $providerFee;

    public function getId(){
    	return $this->id;
    }

    public function getElementId(){
        return $this->elementId;
    }

    public function getElementType(){
    	return $this->elementType;
    }

    public function getDate(){
    	return $this->date;
    }

    public function getClientRate(){
    	return $this->clientRate;
    }

    public function getProviderFee(){
    	return $this->providerFee;
    }

    public function setId($id){
        $this->id = $id;
    }

    public function setElementId($elementId){
        $this->elementId = $elementId;
    }

    public function setElementType($elementType){
        $this->elementType = $elementType;
    }

    public function setDate($date){
        $this->date = $date;
    }

    public function setClientRate($clientRate){
        $this->clientRate = $clientRate;
    }

    public function setProviderFee($providerFee){
        $this->providerFee = $providerFee;
    }

}