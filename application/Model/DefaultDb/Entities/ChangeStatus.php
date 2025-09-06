<?php

/**
 * @Entity(repositoryClass="DefaultDb_Repositories_ChangeStatusRepository")
 * @Table(name="change_status")
 */
class DefaultDb_Entities_ChangeStatus
{
    // definicion de las constantes
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_BLOCK = 2;

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
     * @Column(type="string", name="new_status")
     * @var string
     */
    protected $newStatus;
    /**
     * @Column(type="string", name="comment")
     * @var string
     */
    protected $comment;
    /**
     * @Column(type="datetime", name="date_change")
     * @var DateTime
     */
    protected $dateChange;

    public function setId($id)
    {
        $this->id = $id;
    }
    
    public function setUser($user)
    {
        $this->user = $user;
    }
    
    public function setNewStatus($newStatus)
    {
        $this->newStatus = $newStatus;
    }
    
    public function setComment($comment)
    {
        $this->comment = $comment;
    }
    
    public function setDateChange($dateChange)
    {
        $this->dateChange = $dateChange;
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getUser()
    {
        return $this->user;
    }
   
    public function getNewStatus()
    {
        return $this->newStatus;
    }
    public function getComment()
    {
        return $this->comment;
    }
    
    public function getDateChange()
    {
        return $this->dateChange;
    }
    
}