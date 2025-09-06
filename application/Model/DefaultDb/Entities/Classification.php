<?php

/**
 * @Entity(repositoryClass="DefaultDb_Repositories_ClassificationRepository")
 * @Table(name="classification")
 */

class DefaultDb_Entities_Classification{
    
    /**
     * @Id @GeneratedValue @Column(type="integer", name="id")
     * @var integer
     */
    protected $id;
    
    /**
     * @Column (type="integer", name="size")
     * @var integer
     */
    protected $size;
    
    /**
     * @Column (type="integer", name="activity")
     * @var integer
     */
    protected $activity;
    
    /**
     * @Column (type="integer", name="consumption")
     * @var integer
     */
    protected $consumption;
    
    public function getId(){
        return $this->id;
    }
    
    public function getSize(){
        return $this->size;
    }
    
    public function getActivity(){
        return $this->activity;
    }
    
    public function getConsumption(){
        return $this->consumption;
    }

    public function setId($id){
        $this->id = $id;
    }
    
    public function setSize($size){
        $this->size = $size;
    }
    
    public function setActivity($activity){
        $this->activity = $activity;
    }
    
    public function setConsumption($consumption){
        $this->consumption = $consumption;
    }
}
?>
