<?php

/**
 * @Entity(repositoryClass="DefaultDb_Repositories_ConfigurationRepository")
 * @Table(name="configurations")
 */
class DefaultDb_Entities_Configuration
{
    /**
     * @Id @GeneratedValue @Column(type="integer")
     * @var integer
     */
    protected $id;

    /**
     * @Column(type="integer", name="minutes_per_point")
     * @var integer
     */
    protected $minutesPerPoint;

    /**
     * @Column(type="integer", name="costing_base_package_size")
     * Volúmen base del paquete, expresado en cm^3
     * @var integer
     */
    protected $basePackageSize;

    /**
     * @Column(type="decimal", precision=3, scale=2, name="costing_power_factor")
     * Potencia a la que se eleva el tamaño del paquete
     * @var integer
     */
    protected $powerFactor;
    
    /** @Column(type="string")
     * @var string
     */
    private $terms;
    
    /** @Column(type="string")
     * @var string
     */
    private $service;
    
    /** @Column(type="string")
     * @var string
     */
    private $privacy;

    /**
     * @Column(type="decimal", precision=4, scale=2, name="promotion_cost")
     * Costo de la proomoción
     * @var float
     */
    protected $promotionCost;


    public function setId()
    {
        return $this->id;
    }

    public function setMinutesPerPoint($minutesPerPoint)
    {
        $this->minutesPerPoint = $minutesPerPoint;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getMinutesPerPoint()
    {
        return $this->minutesPerPoint;
    }

    public function getBasePackageSize(){
        return $this->basePackageSize;
    }

    public function setBasePackageSize($basePackageSize){
        $this->basePackageSize = $basePackageSize;
    }

    public function getPowerFactor(){
        return $this->powerFactor;
    }

    public function setPowerFactor($powerFactor){
        $this->powerFactor = $powerFactor;
    }
    
    public function getTerms()
    {
    	return $this->terms;
    }
    
    public function getService()
    {
    	return $this->service;
    }
    
    public function getPrivacy()
    {
    	return $this->privacy;
    }
    
    public function getPromotionCost(){
        return $this->promotionCost;
    }

    public function setPromotionCost($promotionCost){
        $this->promotionCost = $promotionCost;
    }

    public function setTerms($terms)
    {
    	$this->terms = $terms;
    }
    
    public function setService($service)
    {
    	$this->service = $service;
    }
    
    public function setPrivacy($privacy)
    {
    	$this->privacy = $privacy;
    }
    
}