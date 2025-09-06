<?php

/**
 * @Entity(repositoryClass="DefaultDb_Repositories_VehicleRepository")
 * @Table(name="vehicles")
 */
class DefaultDb_Entities_Vehicle
{
    const TYPE_UNKNOWN = 0;
    const TYPE_CAJA_SECA = 1;
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    /**
     * @Id @GeneratedValue @Column(type="integer")
     * @var integer
     */
    protected $id;

    /**
     * @Column(type="integer", name="type")
     * @var integer
     */
    protected $type;

    /**
     * @Column(type="float", name="volume")
     * @var float
     */
    protected $volume;

    /**
     * @Column(type="string", name="economic_number")
     * @var string
     */
    protected $economicNumber;

    /**
     * @Column(type="string")
     * @var string
     */
    protected $name;
    /**
     * @Column(type="float", name="capacity")
     * @var float
     */
    protected $capacity;
    
    /**
     * @Column(type="integer", name="status")
     * @var integer
     */
    protected $status = self::STATUS_ACTIVE;

    /**
     * @Column(type="string", name="trade_mark")
     * @var string
     */
    protected $tradeMark;

    /**
     * @Column(type="string", name="plate")
     * @var string
     */
    protected $plate;

    /**
     * @Column(type="string", name="color")
     * @var string
     */
    protected $color;

    /**
     * @Column(type="string", name="gps")
     * @var string
     */
    protected $gps;

    /**
     * @Column(type="string", name="model")
     * @var string
     */
    protected $model;

    /**
     * @Column(type="float", name="width")
     * @var float
     */
    protected $width;

    /**
     * @Column(type="float", name="height")
     * @var float
     */
    protected $height;

    /**
     * @Column(type="float", name="deep")
     * @var float
     */
    protected $deep;

    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_User")
     */
    protected $driver;

    public function getId()
    {
        return $this->id;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getStrType()
    {
        $str = '';
        switch ($this->type)
        {
            case self::TYPE_UNKNOWN:
                $str = 'Desconocido';
                break;
            case self::TYPE_CAJA_SECA:
                $str = 'Caja seca';
                break;
        }
        return $str;
    }

    public function getVolume()
    {
        return $this->volume;
    }

    public function getFormatVolume()
    {
        return $this->volume . ' <abbr title="pies cúbicos">ft³</abbr>';
    }

    public function getEconomicNumber()
    {
        return $this->economicNumber;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getCapacity()
    {
        return $this->capacity;
    }

    public function getFormatCapacity()
    {
        return $this->capacity . ' <abbr title="kilogramos">kg</abbr>';
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getTradeMark()
    {
        return $this->tradeMark;
    }

    public function getPlate()
    {
        return $this->plate;
    }

    public function getColor()
    {
        return $this->color;
    }

    public function getGps()
    {
        return $this->gps;
    }

    public function getModel()
    {
        return $this->model;
    }

    public function getWidth()
    {
        return $this->width;
    }

    public function getFormatWidth()
    {
        return $this->width . ' <abbr title="metros">m</abbr>';
    }

    public function getHeight()
    {
        return $this->height;
    }

    public function getFormatHeight()
    {
        return $this->height . ' <abbr title="metros">m</abbr>';
    }

    public function getDeep()
    {
        return $this->deep;
    }

    public function getFormatDeep()
    {
        return $this->deep . ' <abbr title="metros">m</abbr>';
    }

    public function getDriver()
    {
        return $this->driver;
    }
    
    public function getNameDriver()
    {
        $name = '-----';
        if($this->driver instanceof DefaultDb_Entities_User)
        {
            $name = $this->driver->getFullName();
        }
        return $name;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function setVolume($volume)
    {
        $this->volume = $volume;
    }

    public function setEconomicNumber($economicNumber)
    {
        $this->economicNumber = $economicNumber;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function setCapacity($capacity)
    {
        $this->capacity = $capacity;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }
}