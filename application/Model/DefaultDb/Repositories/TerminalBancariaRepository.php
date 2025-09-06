<?php

use Doctrine\ORM\EntityRepository;


class DefaultDb_Repositories_TerminalBancariaRepository extends EntityRepository 
{
    
    public function fncGuardarTerminalBancariaPago($pago, $datosTerminal) 
    {
        $em = $this->getEntityManager();
        
        $terminalBancaria = new DefaultDb_Entities_TerminalBancaria();
        $terminalBancaria->setPagos($pago);
        $terminalBancaria->setMonto($datosTerminal["compra"]["montoCompra"]);
        $terminalBancaria->setIdTransferencia($pago->getId());
        $terminalBancaria->setUsuario($this->fncGetUsuario());
        
        $em->persist($terminalBancaria);
        $em->flush();       
                
        return $terminalBancaria;
        
    }
    
    private function fncGetUsuario()
    {
        $em = $this->getEntityManager('DefaultDb');
        $userSessionId = $_SESSION['__M3']['MasDistribucion']['Credentials']['id']; 
        $usuario = $em->getRepository('DefaultDb_Entities_User')->find($userSessionId);
        return $usuario;
    }
    
}