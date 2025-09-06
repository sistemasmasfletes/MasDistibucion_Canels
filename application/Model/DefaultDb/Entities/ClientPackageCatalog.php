<?php

/**
 * @Entity(repositoryClass="DefaultDb_Repositories_ClientPackageCatalogRepository")
 * @Table(name="client_package_catalog")
 */
class DefaultDb_Entities_ClientPackageCatalog
{
    /**
     * @Id @GeneratedValue @Column(type="integer")
     * @var integer
     */
    protected $id;

    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_User")
     */
    protected $user;
    
    /**
     * @Column(type="string", length=80,name="name")
     * @var text
     */
    protected $name;

    /**
     * @var text
     */
    protected $status;

    /**
     * @Column(type="decimal", precision=7, scale=2, name="weight", nullable=true)
     * @var text
     */
    protected $weight;

    /**
     * @Column(type="decimal", precision=5, scale=2, name="width")
     * @var text
     */
    protected $width;

    /**
     * @Column(type="decimal", precision=5, scale=2, name="height")
     * @var text
     */
    protected $height;

    /**
     * @Column(type="decimal", precision=5, scale=2, name="depth")
     * @var text
     */
    protected $depth;
   

    /**
     * @Column(type="decimal", precision=11, scale=2, name="size")
     * @var string
     */

    protected $size;


    /**
     * @Column(type="float", name="price", nullable=true)
     * @var double
     */
    protected $price;

    /**
     * @Column(type="string", length=250, name="description", nullable=true)
     * @var string
     */

    protected $description;

    public function getId()
    {
        return $this->id;
    }

    public function getUser()
    {
        return $this->user;
    }
    
    public function getName()
    {
        return $this->name;
    }

    public function getStatus()
    {
        return $this->status;
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

    public function getPrice()
    {
        return $this->price;
    }

    public function getWeight()
    {
        return $this->weight;
    }

    public function getSize()
    {
        return $this->size;
    }    
    
    public function getDescription(){
        return $this->description;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setUser($user)
    {
        $this->user = $user;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function setStatus($status)
    {
        $this->status = $status;
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

    public function setPrice($price)
    {
        $this->price = $price;
    }

    public function setWeight($weight)
    {
        $this->weight = $weight;
    }

    public function setSize($size)
    {
        $this->size = $size;
    }

    public function setDescription($description){
        $this->description = $description;
    }

    /*
     *  Metodo que regresa un combo box con el numero de unidades seleccionadas
     */

    public function getUnitiBox($propietis='name="MyBox"')
    {
        $reg= '<select '.$propietis.'>';
        for($index = 0; $index < 30; $index++)
        {
            $reg .= '<option value="'.$index.'">'.$index.'</option>';
        }
        $reg .= '</select>';
        return $reg;
    }

}