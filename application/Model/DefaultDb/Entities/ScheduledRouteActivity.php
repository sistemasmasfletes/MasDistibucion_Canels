<?php
/**
 * @Entity(repositoryClass="DefaultDb_Repositories_ScheduledRouteActivityRepository")
 * @Table(name="scheduled_route_activity")
 */

class DefaultDb_Entities_ScheduledRouteActivity
{
    /**
     * @Id @GeneratedValue @Column(type="integer", name="id")
     * @var integer
     */
    protected $id;
    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_ScheduledRoute")
     *@JoinColumn(nullable=true)
     */
    protected $scheduledRoute;
    
    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_RoutePointActivity")
     *@JoinColumn(nullable=true)
     */
    protected $routePointActivity;

    public function getId(){
        return $this->id;
    }
    
    public function getScheduledRoute(){
        return $this->scheduledRoute;
    }
     
    public function getRoutePointActivity(){
        return $this->routePointActivity;
    }

    public function setId($id){
        $this->id = $id;
    }
    
    public function setScheduledRoute($scheduledRoute){
        $this->scheduledRoute = $scheduledRoute;
    }

    public function setRoutePointActivity($routePointActivity){
        $this->routePointActivity = $routePointActivity;
    }
    
}
