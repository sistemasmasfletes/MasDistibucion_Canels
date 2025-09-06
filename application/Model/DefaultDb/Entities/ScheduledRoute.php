<?php
/**
 * @Entity(repositoryClass="DefaultDb_Repositories_ScheduledRouteRepository")
 * @Table(name="scheduled_route")
 */

class DefaultDb_Entities_ScheduledRoute{
    /**
     * @Id @GeneratedValue @Column(type="integer", name="id")
     * @var integer
     */
    protected $id;

    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_Schedule")
     *@JoinColumn(nullable=true)
     */
    protected $schedule;

    /**
     * @Column(type="integer", name="schedule_num", nullable=true)
     * @var integer
     */
    protected $scheduleNum;

    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_Route")
     *@JoinColumn(nullable=true)
     */
    protected $route;
    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_User")
     *@JoinColumn(nullable=true)
     */
    protected $driver;
    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_Vehicle")
     *@JoinColumn(nullable=true)
     */
    protected $vehicle;
    /**
     * @Column(type="datetime", name="scheduled_date")
     * @var datetime
     */
    protected $scheduledDate;
    
    /**
     * @Column(type="integer", nullable=true)
     * @var integer
     * 0 Inactiva
     * 1 Activa
     * 2 Cancelada
     */
    protected $status;

    /**
     * @Column(type="integer", nullable=true)
     * @var integer
     */
    protected $progress;

    /**
     * @Column(type="datetime", name="start_date", nullable=true)
     * @var datetime
     */
    protected $startDate;
    
    /**
     * @Column(type="datetime", name="end_date", nullable=true)
     * @var datetime
     */
    protected $endDate;
    
    /**
     * @Column(type="integer", nullable=true)
     * @var integer
     * 0 Sin iniciar
     * 1 En proceso
     * 2 Finalizada
     */
    protected $statusRoute;
    
    public function getId(){
        return $this->id;
    }
    
    public function getSchedule(){
        return $this->schedule;
    }

    public function getScheduleNum(){
        return $this->scheduleNum;
    }

    public function getRoute(){
        return $this->route;
    }
    
    public function getDriver(){
        return $this->driver;
    }
    
    public function getVehicle(){
        return $this->vehicle;
    }
    
    public function getScheduledDate(){
        return $this->scheduledDate;
    }
    
    public function getStatus(){
        return $this->status;
    }

    public function getProgress(){
        return $this->progress;
    }

    public function getStartDate(){
        return $this->startDate;
    }

    public function getEndDate(){
        return $this->endDate;
    }
    
    public function getStatusRoute(){
        return $this->statusRoute;
    }

    public function setId($id){
        $this->id = $id;
    }
    
    public function setSchedule($schedule){
        $this->schedule = $schedule;
    }

    public function setScheduleNum($scheduleNum){
        $this->scheduleNum = $scheduleNum;
    }

    public function setRoute($route){
        $this->route = $route;
    }
    
    public function setDriver($driver){
        $this->driver = $driver;
    }
    
    public function setVehicle($vehicle){
        $this->vehicle = $vehicle;
    }
    
    public function setScheduledDate($scheduledDate){
        $this->scheduledDate = $scheduledDate;
    }

    public function setStatus($status){
        $this->status = $status;
    }

    public function setProgress($progress){
        $this->progress = $progress;
    }

    public function setStartDate($startDate){
        $this->startDate = $startDate;
    }

    public function setEndDate($endDate){
        $this->endDate = $endDate;
    }
    
    public function setStatusRoute($statusRoute){
        $this->statusRoute = $statusRoute;
    }

}
