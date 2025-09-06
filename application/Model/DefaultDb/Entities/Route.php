<?php
/**
 * @Entity(repositoryClass="DefaultDb_Repositories_RouteRepository")
 * @Table(name="routes")
 */
class DefaultDb_Entities_Route
{
    const CLOSE = 1;
    const OPEN = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    /**
     * @Id @GeneratedValue @Column(type="integer")
     * @var integer
     */
    protected $id;
    /**
     * @Column(type="string", name="code")
     * @var string
     */
    protected $code;
    /**
     * @Column(type="string", name="name")
     * @var string
     */
    protected $name;
    /**
     * @Column(type="integer", name="status")
     * @var integer
     */
    protected $status = 1;
    /**
     * @Column(type="string", name="capacity")
     * @var string
     */
    protected $capacity = 0;
    
    /**
     * @Column(type="float", name="factor")
     * @var double
     */
    protected $factor = 0;
    
    /**
     * @OneToMany(targetEntity="DefaultDb_Entities_Schedule", mappedBy="route")
     */
    protected $schedules;

    /**
     * @Column(type="integer", name="close", options={"default" = 0})
     * @var integer
     */
    protected $close;

    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_Zone")
     */
    protected $zone;

    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_User")
     */
    protected $controller;

    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_User")
     */
    protected $franchisee;
    
    public function getFactor()
    {
        return $this->factor;
    }
    
    public function setFactor($factor){
        $this->factor = $factor;
    }
    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getCapacity()
    {
        return $this->capacity;
    }

    public function setId($id)
    {
        $this->id = $id;
    }
    
    public function getCode()
    {
        return $this->code;
    }

    public function setCode($code)
    {
        $this->code = $code;
    }

        public function setName($name)
    {
        $this->name = $name;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function setCapacity($capacity)
    {
        $this->capacity = $capacity;
    }
    
    public function getSchedules()
    {
        return $this->schedules;
    }

    public function setClose($close)
    {
        $close = ($close === true || $close == 1) ? 1 : 0;
        $this->close = $close;
    }

    public function toggleClose()
    {
        $this->close = ($this->close == 1 ? 0 : 1);
    }

    public function getController()
    {
        return $this->controller;
    }

    public function setController($controller)
    {
        $this->controller = $controller;
    }
    
    public function getFranchisee()
    {
    	return $this->franchisee;
    }
    
    public function setFranchisee($franchisee)
    {
    	$this->franchisee = $franchisee;
    }
    

}