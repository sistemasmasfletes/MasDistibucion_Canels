<?php
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use com\masfletes\db\DBUtil;
use Doctrine\ORM\Tools\Pagination\Paginator;

class DefaultDb_Repositories_AprobacionCreditoControladorRepository extends EntityRepository
{
     public function getAprobacionCreditoControladorListDQL($page,$rowsPerPage,$sortField,$sortDir,$id,$usuario,$tipoPago,$montoCompra,$creditos,$fecha,$nombreImg,$path,$moneda,$name,$referencia,$cuenta,$estado,$client){
        $em = $this->getEntityManager();
        $offset = ($page-1)*$rowsPerPage;
        $dql = $em->createQueryBuilder();
        $test='m.name';
        if($sortField == null){

        }else{
            $test = 'm.'+$sortField;
        }
        
        $dql->select('m')
            ->from('DefaultDb_Entities_CompraCreditos', 'm');

        $query=$em->createQuery($dql);

        $AprobacionCreditoControladorCreditos = $query->getResult();
        $paginator = new Paginator($query, $fetchJoinCollection = false);
        $totalRecords = count($AprobacionCreditoControladorCreditos);
        
        $x=0;
        foreach ($AprobacionCreditoControladorCreditos as $cc){
            $resultAprobacionCreditoControlador[$x]=array('id' => $cc->getId(), 'usuario' => $cc->getUsuario(),'tipoPago' => $cc->getTipoPago(), 'montoCompra' => $cc->getMontoCompra(), 'creditos' => $cc->getCreditos(), 'fecha' => $cc->getFecha(), 'nombreImg' => $cc->getNombreImg(), 'path' => $cc->getPath(), 'moneda' => $cc->getMoneda(), 'name' => $cc->getName(), 'referencia' => $cc->getReferencia(), 'cuenta' => $cc->getCuenta(), 'estado' => $cc->getEstado(),'client'=>$cc->getClient());
            
            $x++;
        }  
        $result[0]=$resultAprobacionCreditoControlador;
        $result[1][0]=array('records' => $x, 'page' =>$page, 'totalpages' => null);
        return $result;
    }
    
    
    
    public function getAprobacionCreditoControlador($id){
        $em = $this->getEntityManager();

        $dql = "SELECT m FROM DefaultDb_Entities_CompraCreditos m WHERE m.id = :id ";

        $query=$em->createQuery($dql);
        $query->setParameter('id',$id);

        $AprobacionCreditoControladorCreditos = $query->getFirstResult();
        return $AprobacionCreditoControladorCreditos;
    }
    
    public function addAprobacionCreditoControlador ($id,$usuario,$tipoPago,$montoCompra,$creditos,$fecha,$nombreImg,$path,$moneda,$name,$referencia,$cuenta,$estado,$client)
    {
        $em = $this->getEntityManager();
        if ($id == null){
            $AprobacionCreditoControladorCreditos = new DefaultDb_Entities_CompraCreditos();
        }else{
            $AprobacionCreditoControladorCreditos=$this->find($id);
        }   
        $AprobacionCreditoControladorCreditos->setUsuario($usuario);
        $AprobacionCreditoControladorCreditos->setTipoPago($tipoPago);
        $AprobacionCreditoControladorCreditos->setMontoCompra($montoCompra);
        if ($moneda=="USD") {
            $q= $em->getRepository('DefaultDb_Entities_Conversion')->createQueryBuilder('c')->select('c.compra')->Where("c.moneda='".$moneda."'")->getQuery()->getResult(); 
            $q2= $em->getRepository('DefaultDb_Entities_Conversion')->createQueryBuilder('c')->select('c.creditos')->Where("c.moneda='".$moneda."'")->getQuery()->getResult(); 
       
            $creditos=((($montoCompra)*$q2[0]['creditos'])/$q[0]['compra']);
            
        }
        else {
            $q= $em->getRepository('DefaultDb_Entities_Conversion')->createQueryBuilder('c')->select('c.compra')->Where("c.moneda='".$moneda."'")->getQuery()->getResult(); 
            $q2= $em->getRepository('DefaultDb_Entities_Conversion')->createQueryBuilder('c')->select('c.compra')->Where("c.moneda='USD'")->getQuery()->getResult(); 
            $q3= $em->getRepository('DefaultDb_Entities_Conversion')->createQueryBuilder('c')->select('c.creditos')->Where("c.moneda='USD'")->getQuery()->getResult();
            
            $USD=((($montoCompra)*$q2[0]['compra'])/$q[0]['compra']);
            $creditos=((($USD)*$q3[0]['creditos'])/$q2[0]['compra']);
            
            
        }
        
        $AprobacionCreditoControladorCreditos->setCreditos($creditos);
        $AprobacionCreditoControladorCreditos->setFecha($fecha);
        $AprobacionCreditoControladorCreditos->setNombreImg($nombreImg);
        $AprobacionCreditoControladorCreditos->setPath($path);
        $AprobacionCreditoControladorCreditos->setMoneda($moneda);
        $AprobacionCreditoControladorCreditos->setName($name);
        $AprobacionCreditoControladorCreditos->setReferencia($referencia);
        $AprobacionCreditoControladorCreditos->setCuenta($cuenta);
        $AprobacionCreditoControladorCreditos->setEstado($estado);
        $AprobacionCreditoControladorCreditos->setClient($client);
        
        $em->persist($AprobacionCreditoControladorCreditos);
        
        $em->flush();
        
        return;
        
        
    }
    
    public function deleteAprobacionCreditoControlador($id)
    {
        $em = $this->getEntityManager();
        if($id == null)
        {
            return;
        }else
        {
            $AprobacionCreditoControlador = $this->find($id);
            $em->remove($AprobacionCreditoControlador);
            $em->flush();
            return;
        }
    }
}
