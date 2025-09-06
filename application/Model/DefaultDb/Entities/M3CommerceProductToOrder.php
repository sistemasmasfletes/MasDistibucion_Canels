<?php

/**
 * @Entity(repositoryClass="DefaultDb_Repositories_M3CommerceProductToOrderRepository")
 * @Table(name="m3_commerce_products_to_orders")
 */
class DefaultDb_Entities_M3CommerceProductToOrder {

    /**
     * @Id @GeneratedValue @Column(type="integer")
     * @var integer
     */
    protected $id;

    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_Product")
     */
    protected $product;
    
    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_ProductVariants")
     **/
   protected $variant;
    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_M3CommerceOrder")
     */
    protected $order;

    /**
     * @Column(type="integer")
     * @var int
     */
    protected $quantity;

    /**
     * @Column(type="float") 
     * @var double
     */
    protected $price;
    /**
     * @OneToMany(targetEntity="DefaultDb_Entities_ProductImages", mappedBy="product")
     * @OrderBy({"id" = "DESC"})
     **/
    private $images;

    
    
    public function setImages($images)
    {
        $this->images = $images;
    }

    public function getImages()
    {
        return $this->images;
    }

        public function getId()
    {
        return $this->id;
    }

    public function getProduct() {
        return $this->product;
    }

    public function getOrder() {
        return $this->order;
    }

    public function getQuantity() {
        return $this->quantity;
    }

    public function getPrice() {
        return $this->price;
    }
    
    public function getVariant(){
        return $this->variant;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setProduct($product) {
        $this->product = $product;
    }

    public function setOrder($order) {
        $this->order = $order;
    }

    public function setQuantity($quantity) {
        $this->quantity = $quantity;
    }

    public function setPrice($price) {
        $this->price = $price;
    }
    public function setVariant($variant){
        $this->variant = $variant;
    }

}
