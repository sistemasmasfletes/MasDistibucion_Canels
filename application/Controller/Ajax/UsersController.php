<?php

class Ajax_UsersController extends Model3_Controller
{

    public function init()
    {
        $this->view->setUseTemplate(false);
    }

    public function listAction()
    {
        $credentials = Model3_Auth::getCredentials();
        $dbs = Model3_Registry::getInstance()->get('databases');
        $em = $dbs['DefaultDb'];
        /* @var $em Doctrine\ORM\EntityManager */
        
        $page = 1;
        $limit = 20;
        if ($this->getRequest()->isPost())
        {
            $post = $this->getRequest()->getPost();
            if (isset($post['page']))
                $page = $post['page'];
            if (isset($post['rows']))
                $limit = $post['rows'];
        }
        
        $totalUsers = $em->getRepository('DefaultDb_Entities_User')->countUsers();
        $totalUsers = $totalUsers[0]['total'];
        $totalPages = $totalUsers / $limit + 1;
        
        if ($page > $totalPages)
            $page = 1;
        
        $this->view->users = $em->getRepository('DefaultDb_Entities_User')->findCommonUsers($limit, $page);
        $user1 = $em->find('DefaultDb_Entities_User', $credentials['id']);
        $this->view->user1 = $user1;
        $this->view->users1 = $user1->getFavorites();
        $this->view->totalPages = $totalPages;
        $this->view->current = $page;
    }

    public function searchAction()
    {
        $limit = 20;

        $credentials = Model3_Auth::getCredentials();
        $dbs = Model3_Registry::getInstance()->get('databases');
        $em = $dbs['DefaultDb'];
        /* @var $em Doctrine\ORM\EntityManager */

        if ($this->getRequest()->isPost())
        {
            $post = $this->getRequest()->getPost();
            $search = $post['search'];
            $user1 = $em->find('DefaultDb_Entities_User', $credentials['id']);
            $this->view->user1 = $user1;
         //   var_dump($this->view->user1);
            $this->view->users = $em->getRepository('DefaultDb_Entities_User')->searchUsersByAll($search);
            $this->view->users1 = $user1->getFavorites();
        }
    }
    
    /**
     * Exporta el listado de asistentes al evento en formato csv
     */
    public function exportAtendeesToCSVAction()
    {
        $dbs = Model3_Registry::getInstance()->get('databases');
        $em = $dbs['DefaultDb'];
        /* @var $em Doctrine\ORM\EntityManager */
        $atendees = $em->getRepository('DefaultDb_Entities_User')->findAll();
        
        $csv_sep = ",";
        $csv_end = PHP_EOL; 
        $csv_file = 'awc2012_atendees.csv';  
        $csv = 'Name,Company,Country,Website'.$csv_end;  

        foreach($atendees as $a)
        {
            if($a->getTypeLoginUser() != 1) //Si es diferente al administrador
            {
                $csv .= str_replace(',',' ',$a->getFirstName()).' '.str_replace(',',' ',$a->getLastName()).$csv_sep;
                $csv .= str_replace(',',' ',$a->getCompany()) .$csv_sep;
                if($a->getCountry())
                    $csv .= str_replace(',',' ',$a->getCountry()->getNameCountry()).$csv_sep;
                else
                    $csv .= str_replace(',',' ',' ').$csv_sep;
                $csv .= str_replace(',',' ',( $a->getCompanyUrl()!= NULL ? 'http://'.$a->getCompanyUrl() : '' )).$csv_end;
            }
        }
                
        if (!$handle = fopen($csv_file, "w+")) 
        {  
            echo "Cannot open/create file";
            die;
        }
        else
        {
            if (fwrite($handle, $csv) === FALSE) 
            {  
                echo "Cannot write to file";                              
            }
            
            fclose($handle);              
        }
        
        header("Content-Type: application/csv; utf-8") ;        
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Last-Modified: '.date('D, d M Y H:i:s'));
        header('Content-Disposition: attachment; filename="'.$csv_file.'"');
        header("Content-Length: ".filesize($csv_file));
        readfile($csv_file);
        exit;        
    }
    
    public function getDataUserAction(){
        $em = $this->getEntityManager('DefaultDb');
        if($this->getRequest()->isPost()){
            $data=$this->getRequest()->getPost();
            $user = $em->getRepository('DefaultDb_Entities_User')->find($data['userId']);
            $response = new stdClass();
            $response->res=false;
            if($user){
                $response->res=true;
                $response->nombre = $user->getFirstName();
                $response->apellido = $user->getLastName();
                $response->phone = $user->getLocalNumber();
                $response->movil = $user->getCellPhone();
            }
            $this->view->response = json_encode($response);
        }
    }
    
    public function typeClientAction()
    {
        $dbs = Model3_Registry::getInstance()->get('databases');
        $em = $dbs['DefaultDb'];
        /* @var $em Doctrine\ORM\EntityManager */
        $routeAdapter = $em->getRepository('DefaultDb_Entities_Route');
        $categoryAdapter = $em->getRepository('DefaultDb_Entities_Category');
       

        $this->view->routes = $routeAdapter->findAll();
        $this->view->category = $categoryAdapter->findAll();
        
    }
    
    public function changeRouteAction()
    {
       $dbs = Model3_Registry::getInstance()->get('databases');
        $em = $dbs['DefaultDb'];
        /* @var $em Doctrine\ORM\EntityManager */
        $routeToPoint = $em->getRepository('DefaultDb_Entities_RoutePoint');
        $this->view->point = null;
        
        if ($this->getRequest()->isPost())
        {
            $post = $this->getRequest()->getPost();
            $point = $routeToPoint->findBy(array('route' => $post['route']));
            $this->view->point = $point;
        }
        
    }
}

?>
