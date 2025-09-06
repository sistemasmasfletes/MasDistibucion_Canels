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
                $product->setCatalog($data['catalog']);
                $product->setName($data['name']);
                $product->setDescription($data['description']);

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
                    //$product->setStatus(DefaultDb_Entities_Product::STATUS_ACTIVE);
                    //$product->setVariantsUse($data['variantsUse']);
                    //$product->setVisible(DefaultDb_Entities_Product::VISIBLE_YES);
                    $product->setNewStartDate($data['newStartDate']);
                    $product->setNewEndDate($data['newEndDate']);
                    $product->setOrder($data['order']);
                    $product->setFeatured($data['featured']);
                }
            }

            $em->persist($product);


            /*
              //Asociar un empaquetado con el producto.
              //Determinar si el producto ya tiene asociación previa con un paquete.
              $packageProductRepo = $em->getRepository('DefaultDb_Entities_PackageProduct');
              $packageProduct = $packageProductRepo->findOneBy(array('product'=>$product->getId(),'defaultPackage'=>1));

              //Si no hay asociación con un paquete, se crea la asociación.
              if($packageProduct==null){
              $packageClient = new DefaultDb_Entities_ClientPackageCatalog();
              $packageProduct = new DefaultDb_Entities_PackageProduct();

              $packageClient->setUser($product->getClient());
              $packageClient->setName($data['pkgName']);
              $packageClient->setWeight($data['pkgWeight']);
              $packageClient->setWidth($data['pkgWidth']);
              $packageClient->setHeight($data['pkgHeight']);
              $packageClient->setDepth($data['pkgDepth']);
              $packageClient->setSize($data['pkgSize']);
              $packageClient->setDescription($data['pkgDescription']);
              $em->persist($packageClient);

              $packageProduct->setPackage($packageClient);
              $packageProduct->setProduct($product);
              $packageProduct->setQuantity($data['pkgQuantity']);
              $packageProduct->setDefaultPackage(1);
              $em->persist($packageProduct);
              }else{
              $pkg = $packageProduct->getPackage();
              $packageProduct->setQuantity($data['pkgQuantity']);
              $pkg->setName($data['pkgName']);
              $pkg->setWeight($data['pkgWeight']);
              $pkg->setWidth($data['pkgWidth']);
              $pkg->setHeight($data['pkgHeight']);
              $pkg->setDepth($data['pkgDepth']);
              $pkg->setSize($data['pkgSize']);
              $pkg->setDescription($data['pkgDescription']);
              } */

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
            $product->setStatus(DefaultDb_Entities_Product::STATUS_ACTIVE);
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
