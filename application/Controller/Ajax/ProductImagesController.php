<?php

class Ajax_ProductImagesController extends Model3_Controller
{

    public function init()
    {
        $this->view->setUseTemplate(false);
    }
    
    public function indexAction()
    {
        $credentials = Model3_Auth::getCredentials();
        $dbs = Model3_Registry::getInstance()->get('databases');
        $em = $dbs['DefaultDb'];
        /* @var $em Doctrine\ORM\EntityManager */
        
        if($this->getRequest()->isPost())
        {
            $productImagesRepos = $em->getRepository('DefaultDb_Entities_ProductImages');
            $post = $this->getRequest()->getPost();
            $idProduct = $post['idProduct'];
            $images = $productImagesRepos->findBy(array('product' => $idProduct));
            $this->view->images = $images;
        }
    }
    
    public function deleteImageAction(){
        $response=array('success'=>false,'path'=>'');
        try{
            $dbs = Model3_Registry::getInstance()->get('databases');
            $em = $dbs['DefaultDb'];

            if($this->getRequest()->isPost())
            {
                $post = $this->getRequest()->getPost();
                $imageid = $post['imageid'];
                $productImagesRepos = $em->getRepository('DefaultDb_Entities_ProductImages');
                $obImage = $productImagesRepos->find($imageid);

                if($obImage){
                    $pathImage = /*$this->getView()->getBaseUrlPublic().'/'.*/ $obImage->getPath();
                    $em->remove($obImage);

                 if(file_exists($pathImage))
                     unlink ($pathImage);

                 $em->flush();
                 $response['success']=true;
                 $response['path']=$pathImage;
                }          
            }         
       }catch(Exception $e){
           $response=array('success'=>false,'path'=>'');
       }
       echo json_encode($response);
    }
}