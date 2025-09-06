<?php

use Doctrine\ORM\EntityRepository;


class DefaultDb_Repositories_PaypalRepository extends EntityRepository 
{
    
    public function fncGuardarPaypalPago($pago, $datosPaypal) 
    {
        $em = $this->getEntityManager();
        
        if(isset($datosPaypal["txn_id"]))
        {
            $paypal = new DefaultDb_Entities_Paypal();
            $paypal->setPagos($pago);
            $paypal->setMonto($datosPaypal["mc_gross"]);
            $paypal->setCurrency($datosPaypal["mc_currency"]);
            $paypal->setIdTransferencia($datosPaypal["txn_id"]);

            $em->persist($paypal);
            $em->flush();       

            return $paypal;
        }
        else
        {
            return 'Ocurrio un error';
        }
        
    }
    
}