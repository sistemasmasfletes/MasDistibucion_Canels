<?php

/**
 * @Entity(repositoryClass="DefaultDb_Repositories_M3CommerceOrderRepository")
 * @Table(name="m3_commerce_order")
 */
class DefaultDb_Entities_M3CommerceOrder {

    const PAYMENT_STATUS_NOT_PAID = 0; 
    const PAYMENT_STATUS_PAID = 1;
    const PAYMENT_STATUS_PENDING = 2;
    
    const SHIPPING_STATUS_NOT_SHIPPED = 0;
    const SHIPPING_STATUS_TO_SHIPPED = 1;
    const SHIPPING_STATUS_SHIPPED = 2;
    const SHIPPING_STATUS_DELIVERED = 3;
    const SHIPPING_STATUS_IN_INTERCHANGE_CENTER = 4;
    
    const RECURRENT_ACTIVE = 1;
    const RECURRENT_NOTACTIVE = 0;
    
    const ORDER_STATUS_ACTIVE = 1;
    const ORDER_STATUS_INTACTIVE = 0;
    
    /**
     * @Id @GeneratedValue @Column(type="integer")
     * @var integer
     */
    protected $id;

    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_User")
     */
    protected $buyer;

    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_User")
     */
    protected $seller;

    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_Schedule")
     * @JoinColumn(nullable=true)
     */
    protected $schedule;

    /**
     * @Column(type="datetime", name="creation_date")
     * @var DateTime
     */
    protected $creationDate;

    /**
     * @Column(type="datetime", name="shipping_date",nullable=true )
     * @var DateTime
     */
    protected $shippingDate;

    /**
     * @Column(type="integer", name="payment_status")
     * @var int
     */
    protected $paymentStatus = self::PAYMENT_STATUS_NOT_PAID;

    /**
     * @Column(type="integer", name="shipping_status")
     * @var int 
     */
    protected $shippingStatus = self::SHIPPING_STATUS_NOT_SHIPPED;

    /**
     * @OneToMany(targetEntity="DefaultDb_Entities_M3CommerceProductToOrder", mappedBy="order", cascade={"persist", "remove"})
     * */
    private $products = array();
    /**
     * @OneToMany(targetEntity="DefaultDb_Entities_ProductImages", mappedBy="product")
     * @OrderBy({"id" = "DESC"})
     **/
    private $images;

    /**
     * @OneToMany(targetEntity="DefaultDb_Entities_PackageToOrder", mappedBy="order")
     **/
    private $packages;
    
    /**
     * @Column(type="text", name="comments")
     * @var text
     */
    protected $comments;
    
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
     * @Column(type="integer", name="order_status")
     * @var integer
     */
    protected $orderStatus = self::SHIPPING_STATUS_NOT_SHIPPED;
    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_M3CommerceOrder")
     */
    protected $orderParent;
    
    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_Point")
     */
    protected $pointBuyer;
    
    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_Point")
     */
    protected $pointSeller;
    
    /**
     * @Column(type="text")
     * @var text
     */
    protected $contentdes;
    
    /**
     * @Column(type="text")
     * @var text
     */
    protected $contactres;
    
    /**
     * @Column(type="text")
     * @var text
     */
    protected $contactsend;
    
    /**
     * @Column(type="integer", name="programer_id")
     * @var integer
     */
    protected $programer;
    
    /**
     * @Column(type="text", name="status_exp")
     * @var text
     */
    protected $stexp;

    public function getPackages()
    {
        return $this->packages;
    }

    public function getImages()
    {
        return $this->images;
    }

        public function getId()
    {
        return $this->id;
    }

    public function getBuyer() {
        return $this->buyer;
    }

    public function getSeller() {
        return $this->seller;
    }

    public function getCreationDate() {
        return $this->creationDate;
    }

    public function getPaymentStatus() {
        return $this->paymentStatus;
    }

    public function getPaymentStatusString($status = false) {

        $find = $this->paymentStatus;
        $str = '';

        if($status !== false)
            $find = $status;
        
        switch($find)
        {
            case self::PAYMENT_STATUS_NOT_PAID:
                $str = 'Sin pagar';
                break;
            case self::PAYMENT_STATUS_PAID:
                $str = 'Pagado';
                break;
            case self::PAYMENT_STATUS_PENDING:
                $str = 'Pendiente';
                break;
        }

        return $str;
    }

    public function getShippingStatus() {
        return $this->shippingStatus;
    }

    public function getShippingStatusString($status = false) {
        $find = $this->shippingStatus;
        $str = '';
        if($status !== false)
            $find = $status;

        switch($find)
        {
            case self::SHIPPING_STATUS_NOT_SHIPPED:
                $str = 'En espera';
                break;
            case self::SHIPPING_STATUS_TO_SHIPPED:
                $str = 'Por Recolectar';
                break;
            case self::SHIPPING_STATUS_SHIPPED:
                $str = 'En Ruta';
                break;
            case self::SHIPPING_STATUS_DELIVERED:
                $str = 'Entregado';
                break;
            case self::SHIPPING_STATUS_IN_INTERCHANGE_CENTER:
                $str = 'En centro de intercambio';
                break;
        }

        return $str;
    }

    public function getProducts() {
        return $this->products;
    }
    
    public function addProduct($product) {
        $product->setOrder($this);
        $this->products[] = $product;        
    }

    public function getSchedule()
    {
        return $this->schedule;
    }

    public function getShippingDate()
    {
        return $this->shippingDate;
    }
    
    public function getComments()
    {
        return $this->comments;
    }
    
     public function getMonday()
    {
        return $this->monday;
    }
    
    public function getTuesday()
    {
        return $this->tuesday;
    }
    
    public function getWednesday()
    {
        return $this->wednesday;
    }

    public function getThursday()
    {
        return $this->thursday;
    }

    public function getFriday()
    {
        return $this->friday;
    }
    
    public function getSaturday()
    {
        return $this->saturday;
    }
    
    public function getSunday()
    {
        return $this->sunday;
    }

    public function getRecurrent()
    {
        return $this->recurrent;
    }
    
    public function getWeek()
    {
        return $this->week;
    }
    
    public function getOrderStatus()
    {
        return $this->orderStatus;
    }

    public function getOrderParent()
    {
        return $this->orderParent;
    }        
    
    public function getPointSeller()
    {
        return $this->pointSeller;
    }
    
    public function getPointBuyer()
    {
        return $this->pointBuyer;    
    }
    
    public function setId($id) {
        $this->id = $id;
    }

    public function setBuyer($buyer) {
        $this->buyer = $buyer;
    }

    public function setSeller($seller) {
        $this->seller = $seller;
    }

    public function setCreationDate($creationDate) {
        $this->creationDate = $creationDate;
    }

    public function setPaymentStatus($paymentStatus) {
        $this->paymentStatus = $paymentStatus;
    }

    public function setShippingStatus($shippingStatus) {
        $this->shippingStatus = $shippingStatus;
    }

    public function setProducts($products) {
        $this->products = $products;
    }

    public function setSchedule($schedule)
    {
        $this->schedule = $schedule;
    }

    public function setShippingDate($shippingDate)
    {
        $this->shippingDate = $shippingDate;
    }

    public function setImages($images)
    {
        $this->images = $images;
    }

    public function setComments($comments)
    {
        $this->comments = $comments;
    }
    
    public function setMonday($monday)
    {
        $this->monday = $monday;
    }
    
    public function setTuesday($tuesday)
    {
        $this->tuesday = $tuesday;
    }
    
    public function setWednesday($wednesday)
    {
        $this->wednesday = $wednesday;
    }

    public function setThursday($thursday)
    {
        $this->thursday = $thursday;
    }

    public function setFriday($friday)
    {
        $this->friday = $friday;
    }
    
    public function setSaturday($saturday)
    {
        $this->saturday = $saturday;
    }
    
    public function setSunday($sunday)
    {
        $this->sunday = $sunday;
    }

    public function setRecurrent($recurrent)
    {
        $this->recurrent = $recurrent;
    }
    
    public function setWeek($week)
    {
        $this->week = $week;
    }
    
    public function setOrderStatus($orderStatus)
    {
        $this->orderStatus = $orderStatus;
    }
    
    public function setOrderParent($orderParent)
    {
        $this->orderParent = $orderParent;
    }
    
    public function setPointSeller($pointSeller)
    {
        $this->pointSeller = $pointSeller;
    }        
    
    public function setPointBuyer($pointBuyer)
    {
        $this->pointBuyer = $pointBuyer;
    }
    
    public function getContent()
    {
    	return $this->contentdes;
    }
    
    public function getContactR()
    {
    	return $this->contactres;
    }
    
    public function getContactS()
    {
    	return $this->contactsend;
    }
    
    public function setContent($contentdes)
    {
    	$this->contentdes = $contentdes;
    }
    
    public function setContactR($contactres)
    {
    	$this->contactres = $contactres;
    }
    
    public function setContactS($contactsend)
    {
    	$this->contactsend = $contactsend;
    }
    
    public function getProgramer()
    {
    	return $this->programer;
    }
    
    public function setProgramer($programer)
    {
    	$this->programer = $programer;
    }

    public function getExp()
    {
    	return $this->stexp;
    }
    
    public function setExp($stexp)
    {
    	$this->stexp = $stexp;
    }
    
}
