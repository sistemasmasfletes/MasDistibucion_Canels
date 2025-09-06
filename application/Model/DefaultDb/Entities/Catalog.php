<?php

/**
 * @Entity(repositoryClass="DefaultDb_Repositories_CatalogRepository")
 * @Table(name="catalog")
 */
class DefaultDb_Entities_Catalog
{
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
     * @ManyToOne(targetEntity="DefaultDb_Entities_Catalog")
     */
    protected $catalogFather;
    /**
     * @Column(type="text", name="title")
     * @var integer
     */
    protected $title;

    /**
     * @OneToMany(targetEntity="DefaultDb_Entities_Product", mappedBy="catalog")     * @OrderBy({"name" = "ASC"})
     **/
    private $products;
    /**
     * @Column(type="string", name="urlCatalog")
     * @var string
     */
    protected $urlCatalog;
    
    public function getId()
    {
        return $this->id;
    }

    /**
     *
     * @return DefaultDb_Entities_User
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     *
     * @return DefaultDb_Entities_Catalog
     */
    public function getCatalogFather()
    {
        return $this->catalogFather;
    }

    public function getTitle()
    {
        return $this->title;
    }
    
    public function getUrlCatalog()
    {
        return $this->urlCatalog;
    }
    
    public function setId($id)
    {
        $this->id = $id;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function setCatalogFather($catalog)
    {
        $this->catalogFather = $catalog;
    }

    public function setClient($client)
    {
        $this->client = $client;
    }

    public function getProducts()
    {
        return $this->products;
    }
    
    public function setUrlCatalog($urlCatalog)
    {
        $this->urlCatalog = $urlCatalog; 
    }
}