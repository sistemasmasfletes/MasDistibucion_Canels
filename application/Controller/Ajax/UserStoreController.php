<?php

class Ajax_UserStoreController extends Model3_Controller
{

    public function init()
    {
        $this->view->setUseTemplate(false);
    }

    public function getBranchesUserAction(){
        $this->view->response = array("cero");
        
        if($this->getRequest()->getPostJson()!=null)
        {
            $em = $this->getEntityManager('DefaultDb');
            $post = $this->getRequest()->getPostJson();
            $clientid =  $post['clientid'];
            $branchesUserRepos = $em->getRepository('DefaultDb_Entities_BranchesUser');
            $branches = $branchesUserRepos->findBy(array('client' => $clientid));
            $arrbranches=array();
            foreach ($branches  as $key => $branch) {
                $arrbranches[]=array(
                    "id"=>$branch->getId(),
                    "clientid"=>$branch->getClient() ? $branch->getClient()->getId() : null,
                    "pointid"=>$branch->getPoint()? $branch->getPoint()->getId() : null,
                    "name"=>$branch->getName(),
                    "direction"=>$branch->getDirection(),
                    "nameAddress"=>$branch->getName() ? $branch->getName().', '.($branch->getDirection() ? $branch->getDirection():'') : ''
                );
            }
            $this->view->response=$arrbranches;
        }
    }
}