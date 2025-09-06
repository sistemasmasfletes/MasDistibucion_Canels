<?php

/**
 * @Entity(repositoryClass="DefaultDb_Repositories_ProductImagesRepository")
 * @Table(name="product_images")
 */
class DefaultDb_Entities_ProductImages
{
    const IMG_MAX =  5242880;
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
     * @Column(type="text", name="path")
     * @var integer
     */
    protected $path;

    /**
     * @Column(type="text", name="thumb", nullable=true )
     * @var integer
     */
    protected $thumb;
    
    /**
     * @Column(type="integer", name="index_order")
     * @var integer
     */
    protected $indexOrder;
    
    /**
     * @var DateTime
     */
    protected $creationDate;

    /**
     * @Column(type="text", name="description", nullable=true)
     * @var integer
     */
    protected $description;


    public function getId()
    {
        return $this->id;
    }

    public function getProduct()
    {
        return $this->product;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getThumb()
    {
        return $this->thumb;
    }

    public function getIndexOrder()
    {
        return $this->index;
    }

    public function getCreationDate()
    {
        return $this->creationDate;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setProduct($product)
    {
        $this->product = $product;
    }

    public function setPath($path)
    {
        $this->path = $path;
    }

    public function setThumb($thumb)
    {
        $this->thumb = $thumb;
    }

    public function setIndexOrder($indexOrder)
    {
        $this->indexOrder = $indexOrder;
    }

    public function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }


}