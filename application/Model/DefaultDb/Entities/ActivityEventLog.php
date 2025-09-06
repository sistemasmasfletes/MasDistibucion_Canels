<?php
/**
 * @Entity(repositoryClass="DefaultDb_Repositories_ActivityEventLogRepository")
 * @Table(name="activity_event_log")
 */
class DefaultDb_Entities_ActivityEventLog
{
     /**
     * @Id @GeneratedValue @Column(type="integer", name="id")
     * @var integer
     */
    protected $id;
    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_ActivityDetail")
     *@JoinColumn(nullable=true) 
     */
    protected $activityDetailId;
    
     /**
     * @Column(type="integer", name="status")
     * @var integer
     */
    protected $status;
    
     /**
     * @Column(type="datetime", name="date")
     * @var datetime
     */
    protected $date;
    
    public function getId(){
        return $this->id;
    }
    
    public  function getActivityDetailId(){
        return $this->activityDetailId;
    }
    
    public function getStatus(){
        return $this->status;
    }
    
    public function getDate(){
        return $this->date;
    }

    public function setId($id){
        $this->id = $id;
    }
    
    public  function setActivityDetailId($activityDetailId){
        $this->activityDetailId = $activityDetailId ;
    }
    
    public function setStatus ($status){
        $this->status = $status;
    }
    
    public function setDate($date){
        $this->date = $date;
    }
}