<?php

/**
 * @Entity (repositoryClass="DefaultDb_Repositories_LogUsersVisitedRepository")
 * @Table(name="log_users_visited")
 */
class DefaultDb_Entities_LogUsersVisited
{
    const ACTION_VISITED = 1; //visitado
    const ACTION_GARNER = 2; //recoleccion
    const ACTION_DELIVERED = 3; //entregado
    /**
     * @Id @GeneratedValue @Column(type="integer")
     * @var integer
     */
    protected $id;    
    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_User")
     */
    protected $userDriver;
    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_User")
     */
    protected $userClient;
    /**
     * @Column(type="integer", name="action")
     * @var integer
     */
    protected $action;
    /**
     * @Column(type="date", name="creation_date")
     * @var DateTime
     */
    protected $creationDate;
    
    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }
    public function getAction()
    {
        return $this->action;
    }

    public function setAction($action)
    {
        $this->action = $action;
    }

        public function getUserDriver()
    {
        return $this->userDriver;
    }

    public function setUserDriver($userDriver)
    {
        $this->userDriver = $userDriver;
    }

    public function getUserClient()
    {
        return $this->userClient;
    }

    public function setUserClient($userClient)
    {
        $this->userClient = $userClient;
    }

    public function getCreationDate()
    {
        return $this->creationDate;
    }

    public function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;
    }

}