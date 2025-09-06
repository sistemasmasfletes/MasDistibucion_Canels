<?php
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use com\masfletes\db\DBUtil;

class DefaultDb_Repositories_PackageToOrderRepository extends EntityRepository
{    
    public function addPackage($order , $package, $cantidad,$price, $totalPrice, $dateSend, $packingGenerated, $originalOrder,$promotion)
    {
        $packageToOrder = new DefaultDb_Entities_PackageToOrder();

        if($packageToOrder !== false)
        {   
            $isPackageFromProduct = ($package instanceof DefaultDb_Entities_Product);

            $packageToOrder->setOrder($order);
            $packageToOrder->setNamePackage($isPackageFromProduct ? 'Embalaje para ('.$package->getName().')' : $package->getName());
            $packageToOrder->setWeight($package->getWeight());
            $packageToOrder->setHeight($package->getHeight());
            $packageToOrder->setWidth($package->getWidth());
            $packageToOrder->setDepth($package->getDepth());
            $packageToOrder->setNumPackage($cantidad);
            $packageToOrder->setPrice($price);
            $packageToOrder->setTotalPrice($totalPrice);
            $packageToOrder->setDateSend($dateSend);
            $packageToOrder->setPackagingGenerated($packingGenerated);
            $packageToOrder->setPackage($package instanceof DefaultDb_Entities_ClientPackageCatalog ? $package : null);
            $packageToOrder->setPromotion($promotion);

            $em = $this->getEntityManager();
            $em->persist($packageToOrder);

            //En una compra, el paquete se obtiene de acuerdo al embalaje del producto;
            //por lo que si la orden inicial tiene varios productos; dado el proceso actual;
            //esta orden se separará en varias órdenes como productos tenga.
            //Por consiguiente, los productos restantes de la orden inicial se reasignarán
            //a las nuevas órdenes creadas.
            if($originalOrder!=null && $isPackageFromProduct){                
                $productToOrder = $em->getRepository('DefaultDb_Entities_M3CommerceProductToOrder')->findOneBy(array('product'=>$package->getId(),'order'=>$originalOrder->getId()));
               
                if ($productToOrder!=null){
                  $productToOrder->setOrder($order);
                }
            }

        }
        return $packageToOrder;
    }
    
    public function getInvoicesUntilDate($dateFin)
    {
        $em = $this->getEntityManager();
        $cnx = $this->getEntityManager()->getConnection();
        $res = $cnx->executeQuery('SELECT sum(m.total_price) as total_price, m.packagingGenerated_id, co.shipping_status 
                                   FROM package_to_order m INNER join m3_commerce_order co ON m.order_id = co.id
                                   WHERE m.dateSend < "'.$dateFin .'" and  m.invoice_id is NULL and co.shipping_status = '.  DefaultDb_Entities_M3CommerceOrder::SHIPPING_STATUS_DELIVERED.' 
                                   GROUP BY m.packagingGenerated_id');
        return $res->fetchAll();
     
    }
    
    public function getInvoicesUntilDateAndDateInvoice($dateFin, $dayInvoice)
    {
        $em = $this->getEntityManager();
        $cnx = $this->getEntityManager()->getConnection();
        $query = 'SELECT sum(m.total_price) as total_price, count(co.shipping_status) num_orders, m.packagingGenerated_id, co.shipping_status 
                    FROM package_to_order m 
                    INNER join m3_commerce_order co ON m.order_id = co.id
                    iNNER join users u on u.id = m.packagingGenerated_id 
                    WHERE m.dateSend < "'.$dateFin .'" and  m.invoice_id is NULL 
                        and co.shipping_status = '.  DefaultDb_Entities_M3CommerceOrder::SHIPPING_STATUS_DELIVERED.' 
                        and u.dayInvoice = '.$dayInvoice.'
                    GROUP BY m.packagingGenerated_id';
        echo $query;
        $res = $cnx->executeQuery($query);
        return $res->fetchAll();
     
    }
    
    public function getPackagesToOrdersUntilDateNotInvoice($dateFin, $client)
    {
        $query = $this->getEntityManager()->createQuery("SELECT p 
            FROM DefaultDb_Entities_PackageToOrder p 
            WHERE p.dateSend < '$dateFin' 
            AND p.invoice IS NULL 
            AND p.packagingGenerated = " . $client->getId());
        return $query->getResult();
    }

        public function getDateNoGroup($dateFin)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT m FROM DefaultDb_Entities_PackageToOrder m WHERE m.invoice is NULL and m.dateSend < '.$dateFin);
        return $query->getResult();
    
    }
    
    //FUNCIÓN INVENTARIO ALMACENISTA
    public function getInventoryWarehousemanListDQL($page,$rowsPerPage,$sortField,$sortDir){
        $em = $this->getEntityManager();
        
        if($page==null) $page=1;
        if($rowsPerPage==null) $rowsPerPage=10;

        $offset = ($page-1)*$rowsPerPage;
        
        $this->_cr = Model3_Auth::getCredentials();
        $dbs = Model3_Registry::getInstance()->get('databases');
        $em1 = $dbs['DefaultDb'];
        $user = $em1->find('DefaultDb_Entities_User', $this->_cr['id']);
        $usuario = "" + $this->_cr['id'];
        
        $sqlSelect = "
            distinct
                ord.id as OC,
                p.namePackage as Paquete,
                count(ad.id) as count,
                po.name,
                count(r.id) as countRpaId
        ";
        $query="
            SELECT [FIELDS]
                from package_to_order p 
                inner join m3_commerce_order ord on p.order_id=ord.id
                inner join transactions trans on trans.transaction_id = ord.id 
                inner join routepoint_activity r on trans.id=r.transaction_id
                inner join activity_type typ on typ.id = r.activityType_id
                inner join route_points rp on rp.id = r.routePoint_id
                left join activity_detail ad on r.id=ad.routePointActivity_id
                left join points po on rp.point_id=po.id
                inner join scheduled_route sr on r.scheduledRoute_id=sr.id
                left join users uw on po.id=uw.point_id
                where uw.id=:usuario and r.status=1
                group by ord.id
            [LIMT]
        ";
        $queryCount = str_replace('[FIELDS]', ' COUNT(*) totalRecords ', $query);
       
        $queryCount = str_replace('[LIMT]', " ", $queryCount);
 
        $querySelect = str_replace('[FIELDS]', $sqlSelect, $query);
        $querySelect = str_replace('[LIMT]', " LIMIT ".$rowsPerPage.' OFFSET '.$offset, $querySelect);
        
        $querys = $querySelect."; ".$queryCount;
        
        $conn = $em->getConnection()->getWrappedConnection();
        $stmt = $conn->prepare($querys);
        
        $stmt->bindValue(":usuario",$usuario);
        
        $stmt->execute();
        $result = DBUtil::getResultsetFromStatement($stmt, \PDO::FETCH_NAMED);
        
        return array('data'=>$result[0],'meta'=>array('totalRecords'=>$result[1][0]["totalRecords"]));
    }
    
}
/*
SELECT `packagingGenerated_id` , sum( total_price )
FROM `package_to_order`
WHERE invoice_id IS NULL
AND dateSend <2012 /08 /06
GROUP BY packagingGenerated_id
LIMIT 0 , 30
*/
