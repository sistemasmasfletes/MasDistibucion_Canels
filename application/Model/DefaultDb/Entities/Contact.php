<?php
/**
 * @Entity(repositoryClass="DefaultDb_Repositories_ContactRepository")
 * @Table(name="contact")
 */

class DefaultDb_Entities_Contact {
    
    /**
     * @Id @GeneratedValue @Column(type="integer", name="id")
     * @var integer
     */
    protected $id;

    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_Point")
     * @JoinColumn(name="point_id", referencedColumnName="id")
     */
    protected $pointId;
    
    /**
     * @Column(type="string", name="name")
     * @var string
     */
    protected $name;
    
    /**
     * @Column(type="string", name="job")
     * @var string
     */
    protected $job;
    
    /**
     * @Column(type="string", name="email")
     * @var string
     */
    protected $email;
    
    /**
     * @Column(type="string", name="phone_number")
     * @var string
     */
    protected $phoneNumber;
    
    /**
     * @Column(type="integer", name="status")
     * @var integer
     */
    protected $status;
    
    /**
     * @Column(type="boolean", name="default_contact", nullable=true)
     * @var boolean
     */
    protected $default;


    public function getId(){
        return $this->id;
    }
    
    public function getName(){
        return $this->name;
    }
    
    public function getJob(){
        return $this->job;
    }
    
    public function getEmail(){
        return $this->email;
    }
    
    public function getPhoneNumber(){
        return $this->phoneNumber;
    }
    
    public function getStatus(){
        return $this->status;
    }
    
    public function getPointId(){
        return $this->pointId;
    }
    
    public function getDefault(){
        return $this->default;
    }

    public function setId($id){
        $this->id = $id;
    }
    
    public function setName($name){
        $this->name = $name;
    }
    
    public function setJob($job){
        $this->job = $job;
    }
    
    public function setEmail($email){
        $this->email = $email;
    }
    
    public function setPhoneNumber($phoneNumber){
        $this->phoneNumber = $phoneNumber;
    }
    
    public function setStatus($status){
        $this->status = $status;
    }
    
    public function setPointId($pointId){
        $this->pointId = $pointId;
    }
    
    public function setDefault($default){
        $this->default = $default;
    }
}