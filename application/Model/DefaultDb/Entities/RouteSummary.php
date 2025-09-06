<?php
/**
 * @Entity(repositoryClass="DefaultDb_Repositories_RouteSummaryRepository")
 * @Table(name="resumen_rutas")
 */
class DefaultDb_Entities_RouteSummary
{
    
    /**
     * @Id @Column(type="integer")
     * @var integer
     */
    protected $Id;
    
    /**
     * @Column(type="string", name="Fecha")
     * @var string
     */
    protected $Fecha;
     /**
     * @Column(type="string", name="Hora")
     * @var string
     */
    protected $Hora;
     /**
     * @Column(type="string", name="Nombre")
     * @var string
     */
    protected $Nombre;
     /**
     * @Column(type="string", name="Codigo")
     * @var string
     */
    protected $Codigo;
     /**
     * @Column(type="string", name="vehiculo")
     * @var string
     */
    protected $vehiculo;
     /**
     * @Column(type="string", name="chofer")
     * @var string
     */
    protected $chofer;
    
    public function getId (){
        return $this->Id;
    }

    public function getFecha(){
        return $this->Fecha;
    }
    
    public function getHora(){
        return $this->Hora;
    }
    
    public function getNombre(){
        return $this->Nombre;
    }
    
    public  function getCodigo(){
        return $this->Codigo;
    }
    
    public  function getvehiculo(){
        return $this->vehiculo;
    }
    
    public function getchofer() {
        return $this->chofer;
    }
    
    public function setId($Id){
        $this->Id = $Id;
    }

    public function setFecha($Fecha){
        $this->Fecha = $Fecha;
    }
    
    public function setHora($Hora){
        $this->Hora = $Hora;
    }
    
    public function setNombre($Nombre){
        $this->Nombre = $Nombre;
    }
    
    public  function setCodigo($Codigo){
        $this->Codigo = $Codigo;
    }
    
    public function setvehiculo($vehiculo){
        $this->vehiculo = $vehiculo;
    }
    
    public function setchofer($chofer){
        $this->chofer = $chofer;
    }
}