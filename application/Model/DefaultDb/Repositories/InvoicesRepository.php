<?php
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class DefaultDb_Repositories_InvoicesRepository extends EntityRepository
{    
     
    public function getDateBetween($dateIni, $dateFin)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT m FROM DefaultDb_Entities_Invoices m WHERE m.cutDate BETWEEN '.$dateIni.' and '.$dateFin);
        return $query->getResult();
    }
    
    public function getInvoiceExpired($dateOfCourt)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT m FROM DefaultDb_Entities_Invoices m WHERE m.generatedInvoice < \''.$dateOfCourt->format("Y-m-d").'\' and m.status = 0 ');
        return $query->getResult();
        
    }
}
