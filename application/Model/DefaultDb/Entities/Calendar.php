<?php

/**
 * @Entity(repositoryClass="DefaultDb_Repositories_CalendarRepository")
 * @Table(name="calendar")
 */
class DefaultDb_Entities_Calendar
{
    /* constantes para estatus*/
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 2;
    const STATUS_DELETED = 3;
    
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
     * @Column(type="string", name="description")
     * @var string
     */
    protected $description;
    /**
     * @Column(type="date", name="start_date")
     * @var DateTime
     */
    protected $startDate;
    /**
     * @Column(type="date", name="end_date")
     * @var DateTime
     */
    protected $endDate;
    /**
     * @Column(type="integer", name="status")
     * @var integer
     */
    protected $status;
    
    public function getId()
    {
        return $this->id;
    }

    public function getRoute()
    {
        return $this->route;
    }

    public function getDescription()
    {
        return $this->description;
    }
    
    public function getStartDate()
    {
        return $this->startDate;
    }

    public function getEndDate()
    {
        return $this->endDate;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setRoute($route)
    {
        $this->route = $route;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
    }

    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }
}