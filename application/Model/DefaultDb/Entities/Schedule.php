<?php

/**
 * @Entity(repositoryClass="DefaultDb_Repositories_ScheduleRepository")
 * @Table(name="schedule")
 */
class DefaultDb_Entities_Schedule
{
    const RECURRENT_ACTIVE = 1;  
    const RECURRENT_INACTIVE = 0;
    const RECURRENT_WEEK_ACTIVE = 1;
    const RECURRENT_WEEK_INACTIVE = 0;
    const DAY_ACTIVE = 1;
    const DAY_INACTIVE = 0;
    const STATUS_ACTIVE = 1 ;
    const STATUS_INACTIVE = 0;

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
     * @ManyToOne(targetEntity="DefaultDb_Entities_Vehicle")
     */
    protected $vehicle;
    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_User")
     */
    protected $user;
    /**
     * @Column(type="datetime", name="start_date")
     * @var DateTime
     */
    protected $startDate;

    /**
     * @Column(type="datetime", name="end_date", nullable=true)
     * @var DateTime
     */
    protected $endDate;

    /**
     * @Column(type="integer", name="status")
     * @var integer
     */

    protected $status = 1;
    /**
     * @Column(type="integer", name="monday")
     * @var integer
     */
    protected $monday = 0;
    /**
     * @Column(type="integer", name="tuesday")
     * @var integer
     */
    protected $tuesday = 0;
    /**
     * @Column(type="integer", name="wednesday")
     * @var integer
     */
    protected $wednesday = 0;
    /**
     * @Column(type="integer", name="thursday")
     * @var integer
     */
    protected $thursday = 0;
    /**
     * @Column(type="integer", name="friday")
     * @var integer
     */
    protected $friday = 0;
    /**
     * @Column(type="integer", name="saturday")
     * @var integer
     */
    protected $saturday = 0;
    /**
     * @Column(type="integer", name="sunday")
     * @var integer
     */
    protected $sunday = 0;
    /**
     * @Column(type="integer", name="recurrent")
     * @var integer
     */
    protected $recurrent = 0;
    /**
     * @Column(type="integer", name="week" )
     * @var integer 
     */
    protected $week = 0;

    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_Schedule")
     */
    protected $scheduleParent;

    public
        function setScheduleParent($parent)
    {
        $this->scheduleParent = $parent;
    }

    public
        function getScheduleParent()
    {
        return $this->scheduleParent;
    }

    public
        function getId()
    {
        return $this->id;
    }

    public
        function getRoute()
    {
        return $this->route;
    }

    public
        function getVehicle()
    {
        return $this->vehicle;
    }

    public
        function getUser()
    {
        return $this->user;
    }

    public function getStartDate()
    {
        return $this->startDate;
    }

    public function getEndDate()
    {
        return $this->endDate;
    }

    public
        function getStatus()
    {
        return $this->status;
    }

    public
        function getMonday()
    {
        return $this->monday;
    }

    public
        function getTuesday()
    {
        return $this->tuesday;
    }

    public
        function getWednesday()
    {
        return $this->wednesday;
    }

    public
        function getThursday()
    {
        return $this->thursday;
    }

    public
        function getFriday()
    {
        return $this->friday;
    }

    public
        function getSaturday()
    {
        return $this->saturday;
    }

    public
        function getSunday()
    {
        return $this->sunday;
    }

    public function getRecurrent()
    {
        return $this->recurrent;
    }

    public
        function getWeek()
    {
        return $this->week;
    }

    public
        function setId($id)
    {
        $this->id = $id;
    }

    public
        function setRoute($route)
    {
        $this->route = $route;
    }

    public
        function setVehicle($vehicle)
    {
        $this->vehicle = $vehicle;
    }

    public
        function setUser($user)
    {
        $this->user = $user;
    }

    public
        function setStartDate($startDate)
    {
        $this->startDate = $startDate;
    }

    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
    }


    public
        function setStatus($status)
    {
        $this->status = $status;
    }

    public
        function setMonday($monday)
    {
        $this->monday = $monday;
    }

    public
        function setTuesday($tuesday)
    {
        $this->tuesday = $tuesday;
    }

    public
        function setWednesday($wednesday)
    {
        $this->wednesday = $wednesday;
    }

    public
        function setThursday($thursday)
    {
        $this->thursday = $thursday;
    }

    public
        function setFriday($friday)
    {
        $this->friday = $friday;
    }

    public
        function setSaturday($saturday)
    {
        $this->saturday = $saturday;
    }

    public
        function setSunday($sunday)
    {
        $this->sunday = $sunday;
    }

    public
        function setRecurrent($recurrent)
    {
        $this->recurrent = $recurrent;
    }

    public
        function setWeek($week)
    {
        $this->week = $week;
    }

    /**
     * Regresa la fecha de inicio de el schedule si es recurrente regresa la proxima fecha posible
     * a partir de la fecha que se le pasa si recibe null entonces se toma la fecha actual del sistema
     * si la fecha ya paso regresa la orginal
     * @param type $getOriginal
     * @param type $date
     * @return type
     */
    public
        function getStartDateRecurrent($date = null)
    {
        $startDate = $this->startDate;
        if ($this->recurrent == self::RECURRENT_ACTIVE)
        {
            $startDate = $this->getNextStartDateRecurrent($date);
        }
        return $startDate;
    }

    /**
     * Regresa la proxima fecha recurrente
     * @param DateTime $date
     * @return \DateTime
     */
    private
        function getNextStartDateRecurrent($date = null)
    {
        if ($date === null)
        {
            $date = new DateTime(); //<--Esta es la que hay que verificar si se toma el actual o otra fecha
            //$date->setTime(0,0,0);
        }
        $newdate = $dateBD = new DateTime($this->startDate->format('Y-m-d H:i:s'));
        $nextDay = $this->getDay($date);
        if ($nextDay !== false)
        {

            if ($date > $dateBD)
            {
                if (strtolower($date->format('l')) != $nextDay)
                {
                    $date->modify('next ' . $nextDay);
                }
                else
                {
                    $date->setTime(0, 0, 0);
                    if ($date < new DateTime())
                    {
                        //$nextDay = $this->getDay($date);
                        $date->modify('next ' . $nextDay);
                    }
                }
                $timeTmp = explode(':', $dateBD->format('H:i:s'));
                $timeTmp = (int) $timeTmp['0'] * 3600 + (int) $timeTmp['1'] * 60;
                $interval = new DateInterval('PT' . $timeTmp . 'S');
                $date->add($interval);                
            }
            else
            {
                $date = clone $dateBD; // Lo clonamos para no modificarlo
                if (strtolower($date->format('l')) != $nextDay)
                {
                    $date->modify('next ' . $nextDay);
                }
                $timeTmp = explode(':', $dateBD->format('H:i:s'));
                $timeTmp = (int) $timeTmp['0'] * 3600 + (int) $timeTmp['1'] * 60;
                $interval = new DateInterval('PT' . $timeTmp . 'S');
                $date->add($interval);
            }
            $newdate = $date; // <--Verificar para un caso con el mismo dia
        }
        return $newdate;
    }

    /**
     * Regresa el nombre del dia en caso de que exista un posible siguiente recurrente
     * si solo no se repite por semana 
     * @param type $date
     * @return string|boolean
     */
    private
        function getDay($date)
    {
        $week = array('sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday');
        $inicio = (int) $date->Format('w');
        $key = $inicio;
        $numDays = 0;
        $first = true;
        while ($first || $key != $inicio)
        {
            $first = false;
            if ($key == 7)
            {
                $key = 0;
            }
            if ($this->{$week [$key]} == self::DAY_ACTIVE)
            {
                if ($this->week == self::RECURRENT_WEEK_ACTIVE)
                {
                    return $week [$key];
                }
                else
                {
                    $dateRangeMax = new DateTime($this->startDate->format('Y-m-d H:i:s'));
                    $dateRangeMax->modify('next monday');
                    $dateTemporalActual = new DateTime($date->Format('Y-m-d H:i:s'));
                    $dateTemporalActual->modify('+' . $numDays . ' day');
                    if ($dateTemporalActual < $dateRangeMax)
                    {
                        return $week [$key];
                    }
                }
            }
            if ($key == 0 && $this->week == self::RECURRENT_WEEK_INACTIVE)
            {
                break; //rompemos el ciclo
            }
            $key++;
            $numDays++;
        }
        return false;
    }

}