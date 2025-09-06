<?php
class Ajax_InvoicesUsersController extends Model3_Controller
{
    public function init()
    {
        $this->view->setUseTemplate(false);
    }

    public function indexAction()
    {
        if($this->getRequest()->isPost())
        {
          
            $post = $this->getRequest()->getPost();
            $endDate = $post['endDate'];
            $endDate = explode('-', $endDate);
            $dateFin = $endDate[2].$endDate[1].$endDate[0];

            $em = $this->getEntityManager('DefaultDb');
            $invoicesUsersRepos = $em->getRepository('DefaultDb_Entities_PackageToOrder');
            $userRepos = $em->getRepository('DefaultDb_Entities_User');

            $invoicesUsers = $invoicesUsersRepos->getInvoicesUntilDate($dateFin);
         //   var_dump($invoicesUsers);die;
            foreach ($invoicesUsers as $iU )
            {
                $client = $userRepos->find($iU['packagingGenerated_id']);
                if($client)
                {
                    $date = new DateTime();
                    $invoice = new DefaultDb_Entities_Invoices();
                    $invoice->setCutDate(new DateTime($dateFin));
                    $invoice->setClient($client);
                    $invoice->setNumOrders(3);
                    $invoice->setStatus(0);
                    $invoice->setGeneratedInvoice($date);
                    $invoice->setPriceTotal($iU['total_price']);
                    $em->persist($invoice);
                    
                    $packages = $invoicesUsersRepos->getPackagesToOrdersUntilDateNotInvoice($dateFin, $client);
                    foreach ($packages as $package)
                    {                        
                        $package->setInvoice($invoice);
                    }
                }
                
                $em->flush();
            }
            
        }  
        $this->view->invoicesUsers = $invoicesUsers;
    }
 
    public function invoicesByUserAction()
    {
        $credentials = Model3_Auth::getCredentials();
        $em = $this->getEntityManager('DefaultDb');
        /* @var $em Doctrine\ORM\EntityManager */
        
        //$user1 = $em->find('DefaultDb_Entities_User', $credentials['id']);
        
        if($this->getRequest()->isPost())
        {
            $post = $this->getRequest()->getPost();
            $startDate = $post['startDate'];
            $startDate = explode('-', $startDate);
            $dateIni = $startDate[2].$startDate[1].$startDate[0];
            
            $endDate = $post['endDate'];
            $endDate = explode('-', $endDate);
            $dateFin = $endDate[2].$endDate[1].$endDate[0];

            $invoicesUsersRepos = $em->getRepository('DefaultDb_Entities_Invoices');
            $invoicesUsers = $invoicesUsersRepos->getDateBetween($dateIni,$dateFin);
            
        }  
        $this->view->invoicesUsers = $invoicesUsers;
        $this->view->userId = $credentials['id'];
        
    }
}

?>
