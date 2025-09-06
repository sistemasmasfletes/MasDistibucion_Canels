<?php
/**
 * @Entity
 * @Table(name="promotion_resources")
 */


class DefaultDb_Entities_PromotionResources
{
    const RESOURCE_TYPE_FILE = 1;
    const RESOURCE_TYPE_URL = 2;

    /**
     * @Id @GeneratedValue @Column(type="integer")
     * @var integer
     */
    protected $id;

    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_Promotion")
     */
    protected $promotion;

    /**
     * @Column(type="integer", name="resource_type")
     * @var int
     */
    protected $resourceType;

    /**
     * @Column(type="string", length=250, name="name")
     * @var string
     */
    protected $name;

    /**
     * @Column(type="string", length=400, name="path")
     * @var string
     */
    protected $path;

    public function getId()
    {
        return $this->id;
    }

    public function getPromotion(){
        return $this->promotion;
    }

    public function getResourceType(){
        return $this->resourceType;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setPromotion($promotion){
        $this->promotion = $promotion;
    }

    public function setResourceType($resourceType){
        $this->resourceType=$resourceType;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function setPath($path)
    {
        $this->path=$path;
    }
}