<?php

class Ajax_UserProductsController extends Model3_Controller {

    public function init() {
        $this->view->setUseTemplate(false);
    }

    public function saveAction() {
        $response = new stdClass();
        $response->res = false;
        $response->message = 'No se ha podido guardar el producto, intentelo nuevamente.';
        if ($this->getRequest()->isPost()) {
            $post = $this->getRequest()->getPost();
             $arrError = $this->validProductDates($post);
            if (count($arrError) == 0) {
                $userId = Model3_Auth::getCredentials('id');
                $em = $this->getEntityManager("DefaultDb");
                $productsAdapter = $em->getRepository('DefaultDb_Entities_Product');
                $catalogId = $this->getRequest()->getParam('id');
				
                $post['client'] = $em->find('DefaultDb_Entities_User', $userId);
                $post['catalog'] = $em->find('DefaultDb_Entities_Catalog', $catalogId);
                $post['newStartDate'] = new DateTime($post['newStartDate']);
                $post['newEndDate'] = new DateTime($post['newEndDate']);
                if (!isset($post['stock'])){
                    $post['stock'] = 0;				
				}else{
					//$numvar = str_replace(",","",$post['stock']);
 					$post['stock'] = intval(str_replace(",","",$post['stock']));
				}
				
				$post['warranty'] = intval($post['warranty']);
				$post['order'] = intval($post['order']);
				$post['size'] = floatval($post['size']);
                $productId = $this->getRequest()->getParam('idProduct');

                if ($post['idProducto1'] > 0) {
                    $productId = $post['idProducto1'];
//                    $response->idProduct = $productId;
//                    $response->res = true;
//                    $response->message = 'Producto ya existe.';
                    
                    $product = $productsAdapter->save($productId, $post);
                    $idProduct = $product->getId();
                    $response->idProduct = $idProduct;
                    $response->res = true;
                    $response->message = $productId == null ? 'Producto Creado correctamente' : 'Producto actualizado correctamente';
                } else {
                    $product = $productsAdapter->save(null, $post);
                    $idProduct = $product->getId();
                    $response->idProduct = $idProduct;
                    $response->res = true;
                    $response->message =  'Producto Creado correctamente ';
                }
            } else {
                $response->message = implode("<br>", $arrError);
            }
        }

        $this->view->response = json_encode($response);
    }

    public function updateAction() {
        $response = new stdClass();
        $response->res = false;
        $response->message = 'No se ha podido guardar el producto, intentelo nuevamente.';

        if ($this->getRequest()->isPost()) {
            $post = $this->getRequest()->getPost();
            $arrError = $this->validProductDates($post);
            if (count($arrError) == 0) {
                $userId = Model3_Auth::getCredentials('id');
                $em = $this->getEntityManager("DefaultDb");
                $productsAdapter = $em->getRepository('DefaultDb_Entities_Product');

                $catalogId = $this->getRequest()->getParam('id');
                $productId = $this->getRequest()->getParam('idProduct');
                $post['client'] = $em->find('DefaultDb_Entities_User', $userId);
                $post['catalog'] = $em->find('DefaultDb_Entities_Catalog', $catalogId);
                $post['newStartDate'] = new DateTime($post['newStartDate']);
                $post['newEndDate'] = new DateTime($post['newEndDate']);

                if (isset($post['ids']) && $post['variantsUse'] == DefaultDb_Entities_Product::VARIANTS_USE) { //actualizamos las variantes
                    $productsVariantsAdapter = $em->getRepository('DefaultDb_Entities_ProductVariants');
                    $variants = $this->createArrayVariants($post['ids'], $post['descriptionVariant'], $post['stockVariant']);
                    unset($post['ids']);
                    unset($post['descriptionVariant']);
                    unset($post['stockVariant']);
                    $productsAdapter->updateProduct($productId, $post);
                    $product = $productsAdapter->find($productId);
                    $result = $productsVariantsAdapter->updateVariantProduct($product, $variants);
                } else {  //actualizamos solamente el producto
                    $productsAdapter->updateProduct($productId, $post);
                }
                $response->res = true;
                $response->message = 'Producto actualizado correctamente';
            } else {
                $response->message = implode("<br>", $arrError);
            }
        }

        $this->view->response = json_encode($response);
    }

    private function validProductDates($arrPost) {
        $errors = array();

        if (!isset($arrPost['name'])) {
            $errors[] = 'El campo nombre es obligatorio';
        } elseif (trim($arrPost['name']) == '') {
            $errors[] = 'El campo nombre no debe estar vacio';
        }

        if (!isset($arrPost['price'])) {
            $errors[] = 'El campo precio es obligatorio';
        } elseif (trim($arrPost['price']) == '') {
            $errors[] = 'El campo precio no debe estar vacio';
        }

        if (!isset($arrPost['stock']) && $arrPost['variantsUse'] == DefaultDb_Entities_Product::VARIANTS_NOT_USE) {
            $errors[] = 'El campo existencia es obligatorio';
        } elseif (isset($arrPost['stock']) && trim($arrPost['stock']) == '') {
            $errors[] = 'El campo existencia no debe estar vacio';
        }

        if (!isset($arrPost['width']) || floatval($arrPost['width']) == 0)
            $errors[] = 'El ancho del embalaje debe ser mayor a cero.';

        if (!isset($arrPost['height']) || floatval($arrPost['height']) == 0)
            $errors[] = 'El alto del embalaje debe ser mayor a cero.';

        if (!isset($arrPost['depth']) || floatval($arrPost['depth']) == 0)
            $errors[] = 'El largo del embalaje debe ser mayor a cero.';

        return $errors;
    }

    public function uploadImageAction() {
        $info = array();
        $this->view->setUseTemplate(false);
        $em = $this->getEntityManager("DefaultDb");
        $productsAdapter = $em->getRepository('DefaultDb_Entities_Product');
        $userId = Model3_Auth::getCredentials('id');

        if ($this->getRequest()->isPost()) {

            $post = $this->getRequest()->getPost();
 
            $idProducto = $post['idProducto'];
            $numImgByProd = $post['numImgByProd'];

            $idUsuario = Model3_Auth::getCredentials('id');

            if (!empty($_FILES)) {
                $product = $productsAdapter->find($idProducto);
                if ($product instanceof DefaultDb_Entities_Product) {

                    $upload = $_FILES['archivos'];
                    foreach ($upload['tmp_name'] as $index => $value) {
                        //                    if (count($imagenes) < $numImgByProd)
                        //                    {
                        $info = $this->handle_file_upload(
                                $upload['tmp_name'][$index], $upload['name'][$index], $upload['size'][$index], $upload['type'][$index], $upload['error'][$index], $userId, $idProducto, 0, // index
                                $product
                        );
                        //                    }
                        //                    else
                        //                    {
                        //                        $info['errorImg'] = 'La imagen ' . $upload['name'][$index] . ' no ha sido cargada. Fuera del limite de imagenes permitidas';
                        //                    }
                    }
                } else {
                    $info['errorImg'] = 'El producto no existe';
                }
            }
        }

        $this->view->info = json_encode($info);
    }

    private function handle_file_upload($uploaded_file, $name, $size, $type, $error, $idUsuario, $idProducto, $indexImagenes, $product) {
        $em = $this->getEntityManager("DefaultDb");
        $imagesAdapter = $em->getRepository('DefaultDb_Entities_ProductImages');
        $info = array();

        $dirUpload = 'data/images/usr' . $idUsuario . '/';
        $subTipo = explode('/', $type);
        $ext = explode('.', $name);

        $pathImage = $dirUpload . 'img' . '_' . $idProducto . '_' . date('YmdHis') . '_' . $indexImagenes . '.' . $ext[count($ext) - 1];

        if ($size > DefaultDb_Entities_ProductImages::IMG_MAX) {
            $info['errorImg'] = 'Ocurrio un error al procesar la imagen...';
        } else {
            if ($subTipo[0] == 'image') {
                $fs = new Model3_FileSystem();
                if (!$fs->existeDir($dirUpload)) {
                    $fs->creaDir($dirUpload);
                }

                /*****Funciones optimizar imagenes*******/
                
                //Parametros optimizacion, resoluci�n m�xima permitida
                $max_ancho = 1024;
                $max_alto = 780;
                
                if($type=='image/png' || $type=='image/jpeg' || $type=='image/gif'){
                
                	//Redimensionar
                	$rtOriginal=$uploaded_file;
                
                	if($type=='image/jpeg'){
                		$original = imagecreatefromjpeg($rtOriginal);
                	}else if($type=='image/png'){
                		$original = imagecreatefrompng($rtOriginal);
                	}else if($type=='image/gif'){
                		$original = imagecreatefromgif($rtOriginal);
                	}
                
                	list($ancho,$alto)=getimagesize($rtOriginal);
                
                	$x_ratio = $max_ancho / $ancho;
                	$y_ratio = $max_alto / $alto;
                
                	if( ($ancho <= $max_ancho) && ($alto <= $max_alto) ){
                		$ancho_final = $ancho;
                		$alto_final = $alto;
                	}elseif (($x_ratio * $alto) < $max_alto){
                		$alto_final = ceil($x_ratio * $alto);
                		$ancho_final = $max_ancho;
                	}else{
                		$ancho_final = ceil($y_ratio * $ancho);
                		$alto_final = $max_alto;
                	}
                
                	$lienzo=imagecreatetruecolor($ancho_final,$alto_final);
                
                	imagecopyresampled($lienzo,$original,0,0,0,0,$ancho_final, $alto_final,$ancho,$alto);
                
                	if($type=='image/jpeg'){
                	$res =	imagejpeg($lienzo,$pathImage);
                	}else if($type=='image/png'){
                	$res =	imagepng($lienzo,$pathImage);
                	}else if($type=='image/gif'){
                	$res =	imagegif($lienzo,$pathImage);
                	}
                }
                /*****Terminan Funciones optimizar imagenes*******/
                
                //$res = move_uploaded_file($uploaded_file, $pathImage);//esto es sin optimizar la imagen
                if ($res) {

                    $data = array(
                        'path' => $pathImage,
                        'indexOrder' => $indexImagenes,
                        'product' => $product
                    );

                    $res = $imagesAdapter->addImage($data);
                    $info = array('name' => $name, 'type' => $type, 'size' => $size, 'successImg' => 'La imagen ' . $name . ' ha sido cargada correctamente');

                    $imagenes = $product->getImages();
                    $this->view->imagenes = $imagenes;
                } else {
                    $info['errorImg'] = 'Ocurrio un error al procesar la imagen...';
                }
            } else {
                $info['errorImg'] = 'Solo se pueden subir imagenes. El archivo ' . $name . ' no se cargo';
            }
        }

        return $info;
    }

    public function createArrayVariants($ids, $variants, $stocks) {
        $array = array();
        foreach ($ids as $key => $id)
            $array[$id] = array('description' => $variants[$key], 'stock' => $stocks[$key]);
        return $array;
    }

}

?>
