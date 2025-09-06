<?php

/**
 * @Entity(repositoryClass="DefaultDb_Repositories_ProductRepository")
 * @Table(name="product")
 */
class DefaultDb_Entities_Product
{
    // definicion de las constantes
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_BLOCK = 2;

    const VISIBLE_YES = true;
    const VISIBLE_NO = false;

    const VARIANTS_NOT_USE = false;
    const VARIANTS_USE = true;

    /**
     * @Id @GeneratedValue @Column(type="integer")
     * @var integer
     */
    protected $id;
    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_User")
     */
    protected $client;
    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_Catalog", inversedBy="products")
     * @JoinColumn(name="catalog_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $catalog;
    /**
     * @Column(type="string", length=80,name="name")
     * @var string
     */
    protected $name;
    /**
     * @Column(type="string", length=250, name="description")
     * @var string
     */
    protected $description;
    /**
     * @Column(type="decimal", precision=10, scale=2, name="price")
     * @var float
     */
    protected $price;
    /**
     * @Column(type="decimal", precision=10, scale=2, name="price_list")
     * @var float
     */
    protected $priceList;
     /**
     * @Column(type="decimal", precision=10, scale=2, name="price_creditos")
     * @var float
     */
    protected $priceCreditos;
    /**
     * @Column(type="integer", name="stock")
     * @var integer
     */
    protected $stock;
    /**
     * @Column(type="text", name="provition_time")
     * @var string
     */
    protected $provitionTime;
    /**
     * @Column(type="text", name="maker")
     * @var string
     */
    protected $maker;
    /**
     * @Column(type="string", length=50, name="sku")
     * @var string
     */
    protected $sku;
    /**
     * @Column(type="integer", name="warranty")
     * @var integer
     */
    protected $warranty;
    /**
     * @Column(type="decimal", precision=7, scale=2, name="weight")
     * @var string
     */
    protected $weight;
    /**
     * @Column(type="decimal", precision=5, scale=2, name="width")
     * @var string
     */
    protected $width;
    /**
     * @Column(type="decimal", precision=5, scale=2, name="height")
     * @var string
     */
    protected $height;
    /**
     * @Column(type="decimal", precision=5, scale=2, name="depth")
     * @var string
     */
    protected $depth;
    /**
     * @Column(type="string", length=30, name="color")
     * @var string
     */
    protected $color;
    /**
     * @Column(type="decimal", precision=11, scale=2, name="size", nullable=true)
     * @var string
     */
    protected $size;
    /**
     * @Column(type="integer", name="offer")
     * @var integer
     */

    protected $offer;
    /**
     * @Column(type="integer", name="status")
     * @var integer
     */
    protected $status;
    /**
     * @Column(type="integer", name="variants_use")
     * @var integer
     */
    protected $variantsUse;
    /**
     * @Column(type="integer", name="visible")
     * @var integer
     */
    protected $visible;
    /**
     * @Column(type="datetime", name="new_start_date")
     * @var DateTime
     */
    protected $newStartDate;
    /**
     * @Column(type="datetime", name="new_end_date")
     * @var DateTime
     */
    protected $newEndDate;
    /**
     * @Column(type="integer", name="product_order")
     * @var integer
     */
    protected $order;
    /**
     * @Column(type="integer", name="featured")
     * @var integer
     */
    protected $featured;
    /**
     * @OneToMany(targetEntity="DefaultDb_Entities_ProductImages", mappedBy="product")
     * @OrderBy({"id" = "DESC"})
     **/
    private $images;
    /**
     * @OneToMany(targetEntity="DefaultDb_Entities_ProductVariants", mappedBy="product")
     * @OrderBy({"id" = "ASC"})
     **/
    private $variants;
    
    
    public function setImages($images)
    {
        $this->images = $images;
    }
    public function getVariants()
    {
        return $this->variants;
    }
    public function getImages()
    {
        return $this->images;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getClient()
    {
        return $this->client;
    }

    public function getCatalog()
    {
        return $this->catalog;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function getPriceList()
    {
        return $this->priceList;
    }
    public function getPriceCreditos()
    {
        return $this->priceCreditos;
    }

    public function getStock()
    {
        return $this->stock;
    }

    public function getProvitionTime()
    {
        return $this->provitionTime;
    }

    public function getMaker()
    {
        return $this->maker;
    }

    public function getSku()
    {
        return $this->sku;
    }

    public function getWarranty()
    {
        return $this->warranty;
    }

    public function getWeight()
    {
        return $this->weight;
    }
    
    public function getWidth()
    {
        return $this->width;
    }
    
    public function getHeight()
    {
        return $this->height;
    }
    
    public function getDepth()
    {
        return $this->depth;
    }

    public function getColor()
    {
        return $this->color;
    }

    public function getSize()
    {
        return $this->size;
    }

    public function getOffer()
    {
        return $this->offer;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getVariantsUse()
    {
        return $this->variantsUse;
    }

    public function getVisible()
    {
        return $this->visible;
    }

    public function getNewStartDate()
    {
        return $this->newStartDate;
    }

    public function getNewEndDate()
    {
        return $this->newEndDate;
    }

    public function getOrder()
    {
        return $this->order;
    }

    public function getFeatured()
    {
        return $this->featured;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setClient($client)
    {
        $this->client = $client;
    }

    public function setCatalog($catalog)
    {
        $this->catalog = $catalog;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function setPrice($price)
    {
        $this->price = $price;
    }
    
    public function setPriceCreditos($priceCreditos)
    {
        $this->priceCreditos = $priceCreditos;
    }
    public function setPriceList($priceList)
    {
        $this->priceList = $priceList;
    }

    public function setStock($stock)
    {
        $this->stock = $stock;
    }

    public function setProvitionTime($provitionTime)
    {
        $this->provitionTime = $provitionTime;
    }

    public function setMaker($maker)
    {
        $this->maker = $maker;
    }

    public function setSku($sku)
    {
        $this->sku = $sku;
    }

    public function setWarranty($warranty)
    {
        $this->warranty = $warranty;
    }

    public function setWeight($weight)
    {
        $this->weight = $weight;
    }
    
    public function setWidth($width)
    {
        $this->width = $width;
    }
    
    public function setHeight($height)
    {
        $this->height = $height;
    }
    
    public function setDepth($depth)
    {
        $this->depth = $depth;
    }

    public function setColor($color)
    {
        $this->color = $color;
    }

    public function setSize($size)
    {
        $this->size = $size;
    }

    public function setOffer($offer)
    {
        $this->offer = $offer;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function setVariantsUse($variantsUse)
    {
        $this->variantsUse = $variantsUse;
    }

    public function setVisible($visible)
    {
        $this->visible = $visible;
    }

    public function setNewStartDate($newStartDate)
    {
        $this->newStartDate = $newStartDate;
    }

    public function setNewEndDate($newEndDate)
    {
        $this->newEndDate = $newEndDate;
    }

    public function setOrder($order)
    {
        $this->order = $order;
    }

    public function setFeatured($featured)
    {
        $this->featured = $featured;
    }
    public function setVariants($variants)
    {
        $this->variants = $variants;
    }



}