<?php
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class DefaultDb_Repositories_ProductImagesRepository extends EntityRepository
{    
    public function addImage(array $data)
    {
        $image = new DefaultDb_Entities_ProductImages();

        if($image !== false)
        {
            foreach($data as $key => $value)
            {
                try
                {
                    $aux = 'set'.ucfirst($key);
                    $image->$aux($value);
                }
                catch(Exception $exc)
                {
                }
            }
            //$image->setCreationDate(new DateTime());

            $em = $this->getEntityManager();
            $em->persist($image);
            $em->flush();

        }
    }


    public function getImageByProduct($idProducto)
    {
        $em = $this->getEntityManager("DefaultDb");
        $imagesAdapter = $em->getRepository('DefaultDb_Entities_ProductImages');

    }
}