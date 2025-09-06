<?php

/**
 * @Entity(repositoryClass="DefaultDb_Repositories_ProductVariantsRepository")
 * @Table(name="product_variants")
 */
class DefaultDb_Entities_ProductVariants
{
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
     * @Column(type="string", name="description")
     * @var string
     */
    protected $description;
    /**
     * @Column(type="integer", name="stock")
     * @var integer
     */
    protected $stock;

    public function getId()
    {
        return $this->id;
    }
    public function getProduct()
    {
        return $this->product;
    }
    public function getDescription()
    {
        return $this->description;
    }
    public function getStock()
    {
        return $this->stock;
    }
    public function setId($id)
    {
        $this->id = $id;
    }
    public function setProduct($product){
        $this->product = $product;
    }
    public function setDescription($description)
    {
        $this->description = $description;
    }
    public function setStock($stock)
    {
        $this->stock = $stock;
    }

}