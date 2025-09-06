<?php

/**
 * @Entity(repositoryClass="DefaultDb_Repositories_PackageToOrderRepository")
 * @Table(name="package_to_order")
 */

class DefaultDb_Entities_PackageToOrder {

    /**
     * @Id @GeneratedValue @Column(type="integer")
     * @var integer
     */
    protected $id;

    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_M3CommerceOrder")
     */
    protected $order;
    
    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_Invoices")
     */
    protected $invoice;
     
    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_User")
     */
    protected $packagingGenerated;

    /**
     * @Column(type="integer", name="num_package")
     * @var int
     */
    protected $numPackage;

    /**
     * @Column(type="decimal", precision=7, scale=2, name="price") 
     * @var double
     */
    protected $price;

    /**
     * @Column(type="decimal", precision=7, scale=2, name="total_price")
     * @var double
     */
    protected $totalPrice;
    
   /**
     * @Column(type="datetime", name="dateSend")
     * @var DateTime
     */
    protected $dateSend;
    
    /**
     * @Column(type="text", name="namePackage")
     * @var text
     */
    protected $namePackage;
    
    /**
     * @Column(type="text", name="weight")
     * @var text
     */
    protected $weight;

    /**
     * @Column(type="text", name="width")
     * @var text
     */
    protected $width;

    /**
     * @Column(type="text", name="height")
     * @var text
     */
    protected $height;

    /**
     * @Column(type="text", name="depth")
     * @var text
     */
    protected $depth;

    /**     
     * @ManyToOne(targetEntity="DefaultDb_Entities_ClientPackageCatalog")
     */
    protected $package;
    
    /**     
     * @ManyToOne(targetEntity="DefaultDb_Entities_Promotion")
     */
    protected $promotion;


    public function getId()
    {
        return $this->id;
    }

    public function getOrder()
    {
        return $this->order;
    }

    public function getInvoice()
    {
        return $this->invoice;
    }
      
    public function getNumPackage()
    {
        return $this->numPackage;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function getTotalPrice()
    {
        return $this->totalPrice;
    }
    
    public function getDateSend()
    {
        return $this->dateSend;
    }
    
    public function getPackagingGenerated()
    {
        return $this->packagingGenerated;
    }
    
    public function getNamePackage()
    {
        return $this->namePackage;
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
    
    public function getPackage()
    {
        return $this->package;
    }

    public function getPromotion()
    {
        return $this->promotion;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setOrder($order)
    {
        $this->order = $order;
    }

    public function setInvoice($invoice)
    {
        $this->invoice = $invoice;
    }

    public function setNumPackage($numPackage)
    {
        $this->numPackage = $numPackage;
    }

    public function setPrice($price)
    {
        $this->price = $price;
    }

    public function setTotalPrice($totalPrice)
    {
        $this->totalPrice = $totalPrice;
    }

    public function setDateSend($dateSend)
    {
        $this->dateSend = $dateSend;    
    }
    
    public function setPackagingGenerated($packagingGenerated)
    {
        $this->packagingGenerated = $packagingGenerated;
    }
    
    public function setNamePackage($namePackage)
    {
        $this->namePackage = $namePackage;
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

    public function setPackage($package)
    {
        $this->package=$package;
    }
    
    public function setPromotion($promotion)
    {
        $this->promotion=$promotion;
    }
}
