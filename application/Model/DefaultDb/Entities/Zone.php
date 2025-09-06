<?php
/**
 * @Entity(repositoryClass="DefaultDb_Repositories_ZoneRepository")
 * @Table(name="zone")
 */


class DefaultDb_Entities_Zone{
    /**
     * @Id @GeneratedValue @Column(type="integer")
     * @var integer
     */
    protected $id;

    /**
     * @Column(type="string", length=250, name="name")
     * @var string
     */
    protected $name;

    public function getId()
    {
        return $this->id;
    }

     public function getName()
    {
        return $this->name;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setName($name)
    {
        $this->name = $name;
    }
}