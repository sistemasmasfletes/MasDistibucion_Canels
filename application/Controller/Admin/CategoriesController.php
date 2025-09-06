<?php

class Admin_CategoriesController extends Model3_Scaffold_Controller {

    public function __construct($request) {
        $this->_sc = new Scaffold_DefaultDb_Category();
        parent::__construct($request);
    }

    public function init() {
        if (!Model3_Auth::isAuth()) {
            $this->redirect('Index/index');
        }
    }

    public function indexAction() {
        
    }

    public function addAction() {
        $this->view->getJsManager()->addJs('application/Admin/Categories/saveImage.js');
        $this->view->getJsManager()->addJsVar('urlSaveImage', '\'' . $this->view->url(array('action' => 'saveImage')) . '\'');



        parent::addAction();
    }

    public function editAction() {
        $this->view->getJsManager()->addJs('application/Admin/Categories/saveImage.js');
        $this->view->getJsManager()->addJsVar('urlSaveImage', '\'' . $this->view->url(array('action' => 'saveImage')) . '\'');
        $id = $this->_request->getParams();
        $id = $id['id'];
        
        $dbs = Model3_Registry::getInstance()->get('databases');
        $em = $dbs['DefaultDb'];
        /* @var $em Doctrine\ORM\EntityManager */

        $category = $em->getRepository('DefaultDb_Entities_Category')->findBy(array('id' => $id));
        
        $this->view->category = $category;
        
        
        
        $oldImage = $category[0]->getImagePath();
        
        if(isset($_POST['imagePath']) && $_POST['imagePath'] != $oldImage   ){

            unlink($oldImage);
        }
        
        parent::editAction();
        
    }
    
    public function deleteAction() {
        $id = $this->_request->getParams();
        $id = $id['id'];
        
        $dbs = Model3_Registry::getInstance()->get('databases');
        $em = $dbs['DefaultDb'];
        /* @var $em Doctrine\ORM\EntityManager */

        $category = $em->getRepository('DefaultDb_Entities_Category')->findBy(array('id' => $id));
        
        $this->view->category = $category;
        
        
        
        $imageDir = $category[0]->getImagePath();
        if($imageDir){

            unlink($imageDir);
        }
        parent::deleteAction();
    }

    public function saveImageAction() {

        if (count($_FILES) > 0) {
            $sourcePath = $_FILES['fileToUpload']['tmp_name'];
            $targetPath = 'data/images/categorias/' . $_FILES['fileToUpload']['name'];
            $fs = new Model3_FileSystem();
            if (!$fs->existeDir('data/images/categorias/')) {
                $fs->creaDir('data/images/categorias/');
            }

            $res = move_uploaded_file($sourcePath, $targetPath);
            if ($res) {
                $info = array('name' => $sourcePath, 'successImg' => 'La imagen ' . $sourcePath . ' ha sido cargada correctamente');
                 $_POST['imagePath'] = $targetPath;
                 
                $this->view->urlImagen = $targetPath;
            } else {
                $info['errorImg'] = 'Ocurrio un error al procesar la imagen...';
            }
            return $info;
        }
    }

}
 