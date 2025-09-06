<?php
//require_once ('dompdf/dompdf_config.inc.php');
class User_DompdfController extends Model3_Controller
{
    public function init()
    {
        /*No incluir ningun archivo de css ya que hace que el pdf deje de funcionar*/
    }
    
    public function indexAction()
    {
        $this->view->setUseTemplate(false);
        $idOrder = $this->getRequest()->getParam('id');
        $this->view->order = $idOrder;
        include "phpqrcode/qrlib.php";
        
        $dbs = Model3_Registry::getInstance()->get('databases');
        $em = $dbs['DefaultDb'];
        $ordersAdapter = $em->getRepository('DefaultDb_Entities_M3CommerceOrder');
        $packagesAdapter = $em->getRepository('DefaultDb_Entities_PackageToOrder');
        $productorder = $em->getRepository('DefaultDb_Entities_M3CommerceProductToOrder');
        $sequentialAdapter = $em->getRepository('DefaultDb_Entities_SequentialActivities');
        $routsAdapter = $em->getRepository('DefaultDb_Entities_RoutePoint');
        
        $order = $ordersAdapter->find($idOrder);
        $packs = $packagesAdapter->findBy(array('order'=>$order));
        $rout = $routsAdapter->findBy(array('point'=>$order->getPointBuyer()));
        $sequentials = $sequentialAdapter->findBy(array('order'=>$order),array('shippingDate' => 'asc'));
        $products = $productorder->findBy(array('order' => $idOrder));
        
        /***************CODIGO PARA GENERAR CODIGO QR DE LOS PAQUETES**************/
        $PNG_WEB_DIR = '../public/tempFiles/';
        $matrixPointSize = 10;
        $errorCorrectionLevel = 'L';
        $filename = $PNG_WEB_DIR.'imgqr.png';
        QRcode::png($order->getId(), $filename, $errorCorrectionLevel, $matrixPointSize, 2);
        $qr = '<img style="width:200px; height:200px;" src="'.$PNG_WEB_DIR.basename($filename).'" />';
        $this->view->qrcode = $qr;
        /***************CODIGO PARA GENERAR CODIGO QR DE LOS PAQUETES**************/
        
        $this->view->rout = $rout;
        $this->view->order = $order;
        $this->view->packs = $packs;
        $this->view->sequentials = $sequentials;
        $this->view->products = $products;
    }
}
