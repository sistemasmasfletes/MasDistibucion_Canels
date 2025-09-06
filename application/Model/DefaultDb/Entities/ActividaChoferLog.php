<?php

/**
 * @Entity(repositoryClass="DefaultDb_Repositories_ActividaChoferLogRepository")
 * @Table(name="tblactividachoferlog")
 * @HasLifecycleCallbacks
 */
class DefaultDb_Entities_ActividaChoferLog {

    /**
     * @Id @GeneratedValue @Column(type="integer", name="id")
     * @var integer
     */
    protected $id;

    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_Pagos")
     * @JoinColumn(name="intIdPagos", referencedColumnName="id", nullable=true)
     */
    protected $pago;
    
     /**
     * @Column(type="date",  name="dtdFecha", nullable=true)
     * @var date
     */
    protected $fecha;
    
    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_ActivityType")
     * @JoinColumn(name="intIdActivityType", referencedColumnName="id", nullable=true)
     */
    protected $actividadTipo;
    
    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_User")
     * @JoinColumn(name="intIdUser", referencedColumnName="id", nullable=true)
     */
    protected $usuario;
    
    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_Point")
     * @JoinColumn(name="intIdPuntoActividad", referencedColumnName="id", nullable=true)
     */
    protected $puntoActividad;
    
    /**
     * @Column(type="string", name="chrEstatusPaquete", length=50, nullable=true, options={"default":0})
     * @var string
     */
    protected $estatus;
    

    public function getId() {
        return $this->id;
    }

    public function getPago() {
        return $this->pago;
    }
    
    public function getFecha() {
        return $this->fecha;
    }
    
    public function getActividadTipo() {
        return $this->actividadTipo;
    }
    
    public function getUsuario() {
        return $this->usuario;
    }
    
    public function getPuntoActividad() {
        return $this->puntoActividad;
    }
    
    public function getEstatus() {
        return $this->estatus;
    }
    

    public function setId($id) 
    {
        $this->id = $id;
    }
    
    public function setFecha($fecha) 
    {
        $this->fecha = $fecha;
    }
    
    public function setPago($pago) 
    {
        $this->pago = $pago;
    }
    
    public function setActividadTipo($actividadTipo) 
    {
        $this->actividadTipo = $actividadTipo;
    }
    
    public function setUsuario($usuario) 
    {
        $this->usuario = $usuario;
    }
    
    public function setPuntoActividad($puntoActividad) 
    {
        $this->puntoActividad = $puntoActividad;
    }
    
    public function setEstatus($estatus) {
        $this->estatus = $estatus;
    }
}
