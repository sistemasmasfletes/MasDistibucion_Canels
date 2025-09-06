<?php

/**
 * @Entity(repositoryClass="DefaultDb_Repositories_CategoryRepository")
 * @Table(name="categories")
 */
class DefaultDb_Entities_Category
{
    /**
     * @Id @GeneratedValue @Column(type="integer")
     * @var integer
     */
    protected $id;    
    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_Category")
     */
    protected $categoryFather;
    /**
     * @Column(type="text", name="title")
     * @var integer
     */
    protected $name;
    
    /**
     * @Column(type="text", name="imagePath")
     * @var string
     */
    protected $imagePath;
    /**
     * @OneToMany(targetEntity="DefaultDb_Entities_User", mappedBy="category")
     **/
    private $users;
    
    public function setId($id)
    {
        $this->id = $id;
    }

    public function setCategoryFather($categoryFather)
    {
        $this->categoryFather = $categoryFather;
    }

    public function setName($name)
    {
        $this->name = $name;
    }
    public function setImagePath($imagePath)
    {
        $this->imagePath = $imagePath;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getCategoryFather()
    {
        return $this->categoryFather;
    }

    public function getName()
    {
        return $this->name;
    }
    public function getImagePath()
    {
        return $this->imagePath;
    }

    public function getUsers()
    {
        return $this->users;
    }

}