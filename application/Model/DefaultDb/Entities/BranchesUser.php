<?php
/**
 * BranchesUser Se agregan las sucursales de cada usuario en una tabla uno a muchos 
 *
 * @author lizRod
 */

/**
 * @Entity(repositoryClass="DefaultDb_Repositories_BranchesUserRepository")
 * @Table(name="branches_user")
 */
class DefaultDb_Entities_BranchesUser
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
     * @Column(type="string", name="name")
     * @var text
     */
    protected $name;
    /**
     * @Column(type="string", name="direction")
     * @var direction
     */
    protected $direction;
    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_Point")
     */
    protected $point;
    /**     * @Column(type="string", name="urlmaps")     * @var text     */    protected $urlmaps;    
    
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
   
    public function getName()
    {
        return $this->name;
    }
    
    public function getDirection()
    {
        return $this->direction;
    }
    
    public function getPoint()
    {
        return $this->point;
    }
    
    public function setId($id)
    {
        $this->id = $id;
    }

    public function setClient($client)
    {
        $this->client = $client;
    }
    
    public function setName($name)
    {
        $this->name = $name;
    }
    
    public function setDirection($direction)
    {
        $this->direction = $direction;
    }
  
    public function setPoint($point)
    {
        $this->point = $point;
    }        public function getUrlmaps()    {    	return $this->urlmaps;    }        public function setUrlmaps($urlmaps)    {    	$this->urlmaps = $urlmaps;    }    
}

?>
