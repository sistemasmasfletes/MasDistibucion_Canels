<?php
/**
 * @Entity(repositoryClass="DefaultDb_Repositories_BinnacleRepository")
 * @Table(name="binnacle")
 */

class DefaultDb_Entities_Binnacle{
    
    /**
     * @Id @GeneratedValue @Column(type="integer", name="id")
     * @var integer
     */
    protected $id;
    
    /**
     * @Column(type="integer", name="scheduled_route_id", nullable=true)
     * @var integer
     */
    protected $scheduledRouteId;
    
    /**
     * @Column(type="integer", name="route_id", nullable=true)
     * @var integer
     */
    protected $routeId;
    
    /**
     * @Column(type="integer", name="driver_id", nullable=true)
     * @var integer
     */
    protected $driverId;
    
    /**
     * @Column(type="integer", name="vehicle_id", nullable=true)
     * @var integer
     */
    protected $vehicleId;
    
    /**
     * @Column(type="datetime", name="scheduled_date")
     * @var datetime
     */
    protected $scheduledDate;
    
    /**
     * @Column(type="integer", name="schedule_id", nullable=true)
     * @var integer
     */
    protected $scheduleId;
    
    public function getId(){
        return $this->id;
    }
    
    public function getScheduledRouteId(){
        return $this->scheduledRouteId;
    }
    
    public function getRouteId (){
        return $this->routeId;
    }
    
    public function getDriverId (){
        return $this->driverId;
    }
    
    public function getVehicleId (){
        return $this->vehicleId;
    }
    
    public function getScheduledDate (){
        return $this->scheduledDate;
    }
    
    public function getScheduleId (){
        return $this->scheduleId;
    }
    
    public function setId($id){
        $this->id = $id;
    }
    
    public function setScheduledRouteId($scheduledRouteId){
        $this->scheduledRouteId = $scheduledRouteId;
    }
    
    public function setRouteId($routeId){
        $this->routeId = $routeId;
    }
    
    public function setDriverId($driverId){
        $this->driverId = $driverId;
    }
    
    public function setVehicleId($vehicleId){
        $this->vehicleId = $vehicleId;
    }
    
    public function setScheduledDate($scheduledDate){
        $this->scheduledDate = $scheduledDate;
    }
    
    public function setScheduleId($scheduleId){
        $this->scheduleId = $scheduleId;
    }
}