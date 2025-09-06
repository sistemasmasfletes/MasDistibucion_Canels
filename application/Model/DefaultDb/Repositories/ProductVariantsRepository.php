<?php
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class DefaultDb_Repositories_ProductVariantsRepository extends EntityRepository
{
    public function addVariantsProduct($product,$variantsArray)
    {
        $em = $this->getEntityManager();
        foreach($variantsArray as $variant)
        {
            $newVariant = new DefaultDb_Entities_ProductVariants();
            if($newVariant !== false)
            {
                $newVariant->setDescription($variant['description']);
                $newVariant->setProduct($product);
                $newVariant->setStock($variant['stock']);
            }
            $em->persist($newVariant);
            $em->flush();
        }
        
    }
    public function updateVariantProduct($product, $variantsArray)
    {
        $em = $this->getEntityManager();
        $class = $this->getClassName();
        $this->removeVariants($product,$variantsArray);
        foreach ($variantsArray as $id => $variantArray)
        {
            $variant = false;
            if($id)
            {
                $variant = $this->find($id);
            }
            else
            {
                $variant = $this->findOneBy(array('product'=>$product,'description'=>$variantArray['description']));
            }
            if($variant !== false && $variant instanceof $class)
            {
                if($variant->getDescription($variantArray['description'])!=$variantArray['description']);
                    $variant->setDescription($variantArray['description']);
                if($variant->getStock()!=$variantArray['stock'])
                    $variant->setStock($variantArray['stock']);
                unset ($variantsArray[$id]);
            }
        }
        $em->flush();
        if(count($variantsArray)>0)
            $this->addVariantsProduct($product, $variantsArray); //agregamos las que faltan
         //
         //@todo: Eliminar las que ya no existen
    }
    public function removeVariants($product,$arrayVariants)
    {
        $em = $this->getEntityManager(); 
        $variants = $this->findBy(array('product'=>$product));
        foreach($variants as $variant)
        {
            if (!array_key_exists($variant->getId(), $arrayVariants)) {
                $em->remove($variant);   
            }
        }
        $em->flush();
    }
}