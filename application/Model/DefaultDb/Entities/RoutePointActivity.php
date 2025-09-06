<?php
/**
 * @Entity(repositoryClass="DefaultDb_Repositories_RoutePointActivityRepository")
 * @Table(name="routepoint_activity")
 */

class DefaultDb_Entities_RoutePointActivity{
    
    /**
     * @Id @GeneratedValue @Column(type="integer", name="id")
     * @var integer
     */
    protected $id;

    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_RoutePoint")
     * @JoinColumn(nullable=true)
     */
    protected $routePoint;
    
    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_ActivityType")
     * @JoinColumn(nullable=true)
     */
    protected $activityType;

     /**
     * @Column(type="datetime", name="date", nullable=true)
     * @var DateTime
     */
    protected $date;

    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_Transactions")
     * @JoinColumn(nullable=true)
     * @var integer
     */
    protected $transaction;
    
    /**
     * @Column(type="datetime", name="hora_actual", nullable=true)
     * @var DateTime
     */
    protected $horaActual;
    
    /**
     * @Column(type="integer", name="status", nullable=true)
     * @var integer
     */
    protected $status;
    
    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_User")
     * @JoinColumn(nullable=true)
     */
    protected $userDelivery;
    
    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_User")
     * @JoinColumn(nullable=true)
     */
    protected $userReceiving;
    
    /**
     * @Column(type="string", length=45, name="user_absence", nullable=true)
     * @var string
     */
    protected $userAbsence;
    
    /**
     * @Column(type="string", length=45, name="entity_from", nullable=true)
     * @var string
     */
    protected $entityFrom;
    
    /**
     * @Column(type="string", length=45, name="entity_to", nullable=true)
     * @var string
     */
    protected $entityTo;
    
    /**
     * @Column(type="string", length=45, name="status_reason", nullable=true)
     * @var string
     */
    protected $statusReason;

    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_ScheduledRoute")
     *@JoinColumn(nullable=true)
     */
    protected $scheduledRoute;

    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_ScheduledLog")
     * @JoinColumn(name="schedule_log", referencedColumnName="id")
     */
    protected $scheduledLog;
    
    public function getId(){
        return $this->id;
    }
    
    public function getRoutePoint(){
        return  $this->routePoint;
    } 
    

    public function getActivityType(){
        return $this->activityType;
    }

    public function getDate(){
        return $this->date;
    } 
    
    public function getTransaction(){
        return $this->transaction;
    }
    
    public function getHoraActual(){
        return $this->horaActual;
    }
    
    public function getStatus(){
        return $this->status;
    }
    
    public function getUserDelivery(){
        return $this->userDelivery;
    }
    
    public function getUserReceiving(){
        return $this->userReceiving;
    }
    
    public function getUserAbsence(){
        return $this->userAbsence;
    }
    
    public function getEntityFrom(){
        return $this->entityFrom;
    }
    
    public function getEntityTo (){
        return $this->entityTo;
    }
    
    public function getStatusReason(){
        return $this->statusReason;
    }

    public function getScheduledRoute(){
        return $this->scheduledRoute;
    }

    public function getScheduledLog(){
        return $this->scheduledLog;
    }

    public function setId($id){
        $this->id = $id;
    }
    
     public function setRoutePoint($routePoint){
        $this->routePoint = $routePoint;
    } 
    
    public function setActivityType($activityType){
        $this->activityType = $activityType;
    }

    public function setDate($date){
        $this->date = $date;
    }

    public function setTransaction($transaction){
        $this->transaction = $transaction;
    }
    
    public function setHoraActual($horaActual){
        $this->horaActual = $horaActual;
    }
    
    public function setStatus($status){
        $this->status = $status;
    }

    public function setScheduledRoute($scheduledRoute){
        $this->scheduledRoute = $scheduledRoute;
    }
    
    public function setUserDelivery($userDelivery){
        $this->userDelivery = $userDelivery;
    }
    
    public function setUserReceiving ($userReceiving){
        $this->userReceiving = $userReceiving;
    }
    
    public function setUserAbsence ($userAbsence){
        $this->userAbsence = $userAbsence;
    }
    
    public function setEntityFrom ($entityFrom){
        $this->entityFrom = $entityFrom;
    }
    
    public function setEntityTo($entityTo){
        $this->entityTo = $entityTo;
    }
    
    public function setStatusReason($statusReason){
        $this->statusReason = $statusReason;
    }

    public function setScheduledLog ($scheduledLog){
        $this->scheduledLog = $scheduledLog;
    }
}
