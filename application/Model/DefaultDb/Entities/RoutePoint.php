<?php

/**
 * @Entity(repositoryClass="DefaultDb_Repositories_RoutePointRepository")
 * @Table(name="route_points")
 */
class DefaultDb_Entities_RoutePoint
{
    //status constants
    const STATUS_NORMAL = 1;
    const STATUS_PAUSED = 2;
    const STATUS_CANCELED = 3;
    const REQUIERED = 1;
    const NO_REQUIERED = 0;

    /**
     * @Id @GeneratedValue @Column(type="integer")
     * @var integer
     */
    protected $id;
    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_Route")
     */
    protected $route;
    /**
     * @Column(type="integer", name="order_number")
     * @var integer
     */
    protected $order;
    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_Point", cascade="persist")
     */
    protected $point;
    /**
     * @Column(type="integer", name="status")
     * @var integer
     */
    protected $status;
    /**
     * @Column(type="time", name="arrival_time")
     * @var DateTime
     */
    protected $arrivalTime;

    /**
     * @Column(type="integer", name="required")
     * @var integer
     */
    protected $required;

    public function getId()
    {
        return $this->id;
    }

    /**
     *
     * @return DefaultDb_Entities_Route 
     */
    public function getRoute()
    {
        return $this->route;
    }

    public function getOrder()
    {
        return $this->order;
    }

    /**
     *
     * @return DefaultDb_Entities_Point 
     */
    public function getPoint()
    {
        return $this->point;
    }

    public function getStatus()
    {
        return $this->status;
    }
    
    public function getArrivalTime()
    {
        return $this->arrivalTime;
    }

    /**
     * 
     * @return boolean
     */
    public function getRequired()
    {
        return $this->required == 1;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setRoute($route)
    {
        $this->route = $route;
    }

    public function setOrder($order)
    {
        $this->order = $order;
    }
    
    public function setPoint($point)
    {
        $this->point = $point;
    }    

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function setArrivalTime($arrivalTime)
    {
        $this->arrivalTime = $arrivalTime;
    }
}