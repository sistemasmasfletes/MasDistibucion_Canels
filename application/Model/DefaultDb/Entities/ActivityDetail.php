<?php
/**
 * @Entity(repositoryClass="DefaultDb_Repositories_ActivityDetailRepository")
 * @Table(name="activity_detail")
 */

class DefaultDb_Entities_ActivityDetail
{
    
    /**
     * @Id @GeneratedValue @Column(type="integer", name="id")
     * @var integer
     */
    protected $id;

    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_RoutePointActivity")
     * @JoinColumn(name="routePointActivity_id", referencedColumnName="id")
     */
    protected $routePointActivityId;
    
    /**
     * @Column(type="integer", name="status", nullable=true)
     * @var integer
     */
    protected $status;
    
    /**
     * @Column(type="string", name="uploadFile", nullable=true)
     * @var string
     */
    protected $uploadFile;
    
    /**
     * @Column(type="string", length=45, nullable=true)
     * @var string
     */
    protected $receptor;
    
    /**
     * @Column(type="string", length=200, nullable=true)
     * @var string
     */
    protected $comentarios;
    
    /**
     * @Column(type="datetime", name="date", nullable=true)
     * @var datetime
     */
    protected $date;
    
    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_ScheduledRoute")
     *@JoinColumn(nullable=true)
     */
    protected $scheduledRouteDetailId;
    
    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_Point")
     *@JoinColumn(nullable=true)
     */
    protected $pointId;
    
    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_Causes")
     *@JoinColumn(nullable=true)
     */
    protected $causeId;

    public function getId(){
        return $this->id;
    }
    
    public function getStatus (){
        return $this->status;
    }
    
    public function getUploadFile (){
        return $this->uploadFile;
    }
    
    public function getReceptor (){
        return $this->receptor;
    }
    
    public function getComentarios (){
        return $this->comentarios;
    }
    
    public function getDate(){
        return $this->date;
    }
    
    public function getScheduledRouteDetailId (){
        return $this->scheduledRouteDetailId;
    }
    
    public function getPointId (){
        return $this->pointId;
    }
    
    public function getCauseId (){
        return $this->causeId;
    }

    public function getRoutePointActivityId(){
        return $this->routePointActivityId;
    }

    public function setId($id){
        $this->id = $id;
    }
    
    public function setStatus($status){
        $this->status = $status;
    }
    
    public function setUploadFile($uploadFile){
        $this->uploadFile = $uploadFile;
    }
    
    public function setReceptor ($receptor){
        $this->receptor = $receptor;
    }
    
    public function setComentarios ($comentarios){
        $this->comentarios = $comentarios;
    }
    
    public function setDate($date){
        $this->date = $date;
    }
    
    public function setScheduledRouteDetailId ($scheduledRouteDetailId){
        $this->scheduledRouteDetailId = $scheduledRouteDetailId;
    }
    
    public function setPointId ($pointId){
        $this->pointId = $pointId;
    }
    
    public function setCauseId ($causeId){
        $this->causeId = $causeId;
    }

    public function setRoutePointActivityId ($routePointActivityId){
        $this->routePointActivityId = $routePointActivityId;
    }
}