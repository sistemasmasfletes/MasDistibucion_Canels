<?php
use Doctrine\ORM\EntityRepository;

class DefaultDb_Repositories_M3CommerceProductToOrderRepository extends EntityRepository
{ 
	public function addProduct($product,$variant,$order,$quantity,$price){
		$productToOrder = new DefaultDb_Entities_M3CommerceProductToOrder();
		if($productToOrder!=false){
			$productToOrder->setProduct($product);
			$productToOrder->setVariant($variant);
			$productToOrder->setOrder($order);
			$productToOrder->setQuantity($quantity);
			$productToOrder->setPrice($price);
		}
		$em = $this->getEntityManager();
        $em->persist($productToOrder);
	}
}