<?php

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class DefaultDb_Repositories_ProductRepository extends EntityRepository {

    public function save($productId, $data) {

        $product = null;
        $em = $this->getEntityManager();
        $em->getConnection()->beginTransaction();

        try {
            if ($productId == null) {
                $product = new DefaultDb_Entities_Product();
                $product->setClient($data['client']);
                // $product->setCatalog($data['catalog']);
                if (isset($data['catalog'])) {
                    $catalog = $data['catalog'];
                    if ($catalog instanceof DefaultDb_Entities_Catalog) {
                        $product->setCatalog($catalog);
                    } else {
                        $catalogEntity = $em->getRepository('DefaultDb_Entities_Catalog')->find($catalog);
                        $product->setCatalog($catalogEntity);
                    }
                }
                $product->setName($data['name']);
                $product->setDescription($data['description']);
                if (isset($data['last_name'])) $product->setLastName($data['last_name']);
                if (isset($data['cell'])) $product->setCell($data['cell']);
                if (isset($data['id_payroll'])) $product->setIdPayroll($data['id_payroll']);
                if (isset($data['priority'])) $product->setPriority($data['priority']);      
                if (isset($data['disability'])) $product->setDisability($data['disability']); 
                if (isset($data['gender'])) $product->setGender($data['gender']);            
                if (isset($data['status'])) $product->setStatus($data['status']);
                if (isset($data['biometric'])) $product->setBiometric($data['biometric']);
                if (isset($data['notification_method'])) $product->setNotificationMethod($data['notification_method']);
                if (isset($data['notify_contact'])) $product->setNotifyContact($data['notify_contact']);
                if (isset($data['Clasificacion1'])) $product->setClasificacion1($data['Clasificacion1']);
                if (isset($data['Clasificacion2'])) $product->setClasificacion2($data['Clasificacion2']);
                if (isset($data['Clasificacion3'])) $product->setClasificacion3($data['Clasificacion3']);

                $price = $data['price'];
                $priceList = $data['priceList'];
                $priceCreditos = $data['priceCreditos'];
                $price = str_replace(',', '', $price);
                $priceList = str_replace(',', '', $priceList);
                $priceCreditos = str_replace(',', '', $priceCreditos);

                $product->setPrice($price);
                $product->setPriceList($priceList);
                $product->setPriceCreditos($priceCreditos);
                $product->setStock($data['stock']);
                $product->setProvitionTime($data['provitionTime']);
                $product->setMaker($data['maker']);
                $product->setSku($data['sku']);
                $product->setWarranty($data['warranty']);

                $weight = $data['weight'];
                $weight = str_replace(',', '', $weight);

                $product->setWeight($weight);
                $product->setWidth($data['width']);
                $product->setHeight($data['height']);
                $product->setDepth($data['depth']);
                $product->setColor($data['color']);
                $product->setSize($data['size']);
                $product->setOffer($data['offer']);
                $product->setStatus(DefaultDb_Entities_Product::STATUS_ACTIVE);
                $product->setVariantsUse(/* $data['variantsUse'] */DefaultDb_Entities_Product::VARIANTS_NOT_USE);
                $product->setVisible(DefaultDb_Entities_Product::VISIBLE_YES);
                $product->setNewStartDate($data['newStartDate']);
                $product->setNewEndDate($data['newEndDate']);
                $product->setOrder($data['order']);
                $product->setFeatured($data['featured']);
            } else {
                $product = $this->find($productId);
                $class = $this->getClassName();

                if ($product !== false && $product instanceof DefaultDb_Entities_Product) {
                    $product->setName($data['name']);
                    $product->setDescription($data['description']);
                    // $product->setCatalog($data['catalog']);
                    if (isset($data['catalog'])) {
                        $catalog = $data['catalog'];
                        if ($catalog instanceof DefaultDb_Entities_Catalog) {
                            $product->setCatalog($catalog);
                        } else {
                            $catalogEntity = $em->getRepository('DefaultDb_Entities_Catalog')->find($catalog);
                            $product->setCatalog($catalogEntity);
                        }
                    }
                    if (isset($data['last_name'])) $product->setLastName($data['last_name']);
                    if (isset($data['cell'])) $product->setCell($data['cell']);
                    if (isset($data['id_payroll'])) $product->setIdPayroll($data['id_payroll']);
                    if (isset($data['priority'])) $product->setPriority($data['priority']);       
                    if (isset($data['disability'])) $product->setDisability($data['disability']); 
                    if (isset($data['gender'])) $product->setGender($data['gender']);             
                    if (isset($data['status'])) $product->setStatus($data['status']);             
                    if (isset($data['biometric'])) $product->setBiometric($data['biometric']);
                    if (isset($data['notification_method'])) $product->setNotificationMethod($data['notification_method']);
                    if (isset($data['notify_contact'])) $product->setNotifyContact($data['notify_contact']);
                    if (isset($data['Clasificacion1'])) $product->setClasificacion1($data['Clasificacion1']);
                    if (isset($data['Clasificacion2'])) $product->setClasificacion2($data['Clasificacion2']);
                    if (isset($data['Clasificacion3'])) $product->setClasificacion3($data['Clasificacion3']);

                    $price = $data['price'];
                    $priceList = $data['priceList'];
                    $priceCreditos = $data['priceCreditos'];
                    $price = str_replace(',', '', $price);
                    $priceList = str_replace(',', '', $priceList);
                    $priceCreditos = str_replace(',', '', $priceCreditos);


                    $product->setPrice($price);
                    $product->setPriceList($priceList);
                    $product->setPriceCreditos($priceCreditos);
                    $product->setStock($data['stock']);
                    $product->setProvitionTime($data['provitionTime']);
                    $product->setMaker($data['maker']);
                    $product->setSku($data['sku']);
                    $product->setWarranty($data['warranty']);

                    $weight = $data['weight'];
                    $weight = str_replace(',', '', $weight);

                    $product->setWeight($weight);

                    $product->setWidth($data['width']);
                    $product->setHeight($data['height']);
                    $product->setDepth($data['depth']);
                    $product->setColor($data['color']);
                    $product->setSize($data['size']);
                    $product->setOffer($data['offer']);
                    $product->setStatus($data['status']);
                    //$product->setVariantsUse($data['variantsUse']);
                    //$product->setVisible(DefaultDb_Entities_Product::VISIBLE_YES);
                    $product->setNewStartDate($data['newStartDate']);
                    $product->setNewEndDate($data['newEndDate']);
                    $product->setOrder($data['order']);
                    $product->setFeatured($data['featured']);
                }
            }

            $em->persist($product);

            $em->flush();
            $em->getConnection()->commit();
        } catch (Exception $ex) {
            $em->getConnection()->rollback();
            throw $ex;
        }
        return $product;
    }

    public function addProduct($data) {
        $product = new DefaultDb_Entities_Product();

        if ($product !== false) {
            foreach ($data as $key => $value) {
                try {
                    $aux = 'set' . ucfirst($key);
                    $product->$aux($value);
                } catch (Exception $exc) {
                    
                }
            }

            // algunos datos que no dejamos que controle el usuario
            //$product->setVariantsUse(DefaultDb_Entities_Product::VARIANTS_NOT_USE);
            //$product->setStatus(DefaultDb_Entities_Product::STATUS_ACTIVE);
            
            $product->setVisible(DefaultDb_Entities_Product::VISIBLE_YES);

//            $product->creation_date = date('Y-m-d H:i:s');

            $em = $this->getEntityManager();
            $em->persist($product);
            $em->flush();
        }
        return $product;
    }

    public function updateProduct($productId, $data) {
        $em = $this->getEntityManager();
        $product = $this->find($productId);
        $class = $this->getClassName();
        $reg = false;

        $em->getConnection()->beginTransaction();
        try {
            if ($product !== false && $product instanceof $class) {
                foreach ($data as $key => $value) {
                    try {
                        $aux = 'get' . ucfirst($key);
                        $current = $product->$aux();
                        if ($current != $value) {
                            $aux = 'set' . ucfirst($key);
                            $product->$aux($value);
                        }
                    } catch (Exception $exc) {
                        
                    }
                }
                $em->persist($product);

                // Asociar un empaquetado con el producto.
                //Determinar si el producto ya tiene asociación previa con un paquete.

                $dql = "SELECT COUNT(pp.id) packagedProduct FROM DefaultDb_Entities_PackageProduct pp INNER JOIN pp.package p
                    WHERE pp.product=:product AND pp.quantity=1 AND pp.defaultPackage=1";
                $query = $em->createQuery($dql);
                $query->setParameter("product", $product->getId());
                $count = $query->getSingleScalarResult();

                //
                if (!$count > 0) {
                    $packageClient = new DefaultDb_Entities_ClientPackageCatalog();
                    $packageProduct = new DefaultDb_Entities_PackageProduct();

                    $packageClient->setUser($product->getClient());
                    $packageClient->setName($product->getName());
                    $packageClient->setWeight($product->getWeight());
                    $packageClient->setWidth($product->getWidth());
                    $packageClient->setHeight($product->getHeight());
                    $packageClient->setDepth($product->getDepth());
                    $packageClient->setPrice($product->getPrice());
                    $em->persist($packageClient);

                    $packageProduct->setPackage($packageClient);
                    $packageProduct->setProduct($product);
                    $packageProduct->setQuantity(1);
                    $packageProduct->setDefaultPackage(1);
                    $em->persist($packageProduct);
                }

                $reg = $em->flush();
                $em->getConnection()->commit();
            }
        } catch (Exception $ex) {
            $em->getConnection()->rollback();
            throw $ex;
        }

        return $reg;
    }

}