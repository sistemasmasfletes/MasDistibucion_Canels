<?php

/**
 * @Entity(repositoryClass="DefaultDb_Repositories_SequentialActivitiesRepository")
 * @Table(name="sequential_activities")
 */
class DefaultDb_Entities_SequentialActivities {
    
    /**
     * Indica que el tipo de la actividad sequencial sera una reccoleccion
     */
    const TYPE_RECOLECTION = 1;

    /**
     * Indica que el tipo de la actividad sequencial sera una entrega
     */
    const TYPE_DELIVERY = 2;
    
    /**
     * Sirve para agregar la ruta a actividades sequenciales pero permanece inactiva hasta que se genera la recoleccion 
     */
    const STATUS_INACTIVE = 0;
    
    /**
     * estatus utilizado para cambiar la actividad sequencial cuando se genero la recoleccion.
     */
    const STATUS_ACTIVE = 1;
    
    
    /**
     * @Id @GeneratedValue @Column(type="integer")
     * @var integer
     */
    protected $id;
    
    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_RoutePoint", cascade="persist")
     */
    protected $routePoint;
    
    /**
     * @Column(type="integer", name="type")
     * @var integer
     */
    protected $type;
    
    /**
     * @Column(type="datetime", name="shipping_date")
     * @var DateTime
     */
    protected $shippingDate;
    
    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_M3CommerceOrder", cascade="persist")
     */
    protected $order;
    
    /**
     * Fecha y hora de salida de la ruta 
     * @Column(type="datetime", name="route_date")
     * @var DateTime
     */
    protected $routeDate;
    
    /**
     * @Column(type="datetime", name="shipping_dateact", nullable=true)
     * @var datetime
     */
    protected $shippingDateAct;
    
    public function getRouteDate()
    {
        return $this->routeDate;
    }
    
    public function setRouteDate( $date )
    {
        $this->routeDate = $date;
    }
//    /**
//     * @Column(type="integer", name="status")
//     * @var integer
//     */
//    protected $status;
//    
//    public function getStatus()
//    {
//        return $this->status;
//    }
//    
//    public function setStatus( $status )
//    {
//        $this->status = $status;
//    }

    /**
     * 
     * @return DefaultDb_Entities_M3CommerceOrder
     */
    public function getOrder()
    {
        return $this->order;
    }
    public function setOrder($order)
    {
        $this->order = $order;
    }

    public function getId()
    {
        return $this->id;
    }
    
    /**
     *
     * @return DefaultDb_Entities_RoutePoint
     */
    public function getRoutePoint()
    {
        return $this->routePoint;
    }
    
    public function getType()
    {
        return $this->type;
    }
    
    public function getShippingDate()
    {
        return $this->shippingDate;
    }
    
    public function getShippingDateAct()
    {
        return $this->shippingDateAct;
    }
    
    public function setId($id)
    {
        $this->id = $id;
    }
    
    public function setRoutePoint($routePoint)
    {
        $this->routePoint = $routePoint;
    }
    
    public function setType($type)
    {
        $this->type = $type ;
    }
    
    public function setShippingDate($shippingDate)
    {
        $this->shippingDate = $shippingDate;
    }
    
    public function setShippingDateAct($shippingDateAct)
    {
    	$this->shippingDateAct = $shippingDateAct;
    }
    
}