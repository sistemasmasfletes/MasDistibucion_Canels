<?php
use com\masfletes\db\DBUtil;

class OperationController_TransactionTypeController extends JController {
    private $userSessionId;
    
    public function init() {
        parent::init();
        if (!Model3_Auth::isAuth()) {
            $this->redirect('Index/index');
        }
        $this->userSessionId = $_SESSION['USERSESSIONID'];
    }
    
    public function getTransactionTypeAction() {
        $params = $this->getRequest()->getPostJson();
 
        $page = $this->getArrayValue('page', $params);
        $rowsPerPage = $this->getArrayValue('rowsPerPage', $params);
        $sortField = $this->getArrayValue('sortField', $params);
        $sortDir = $this->getArrayValue('sortDir', $params);
        $filter= $this->getArrayValue('filter',$params);
        $id = $this->getArrayValue('id', $filter);
        $name = $this->getArrayValue('name', $filter);
        
        $em = $this->getEntityManager('DefaultDb');
        $transactionTypeRepo = $em->getRepository('DefaultDb_Entities_TransactionType');
        
        $status=null;
        
        $TransactionType=$transactionTypeRepo->getTransactionTypeListDQL($page,$rowsPerPage,$sortField,$sortDir,            
            $id,
            $name);
        
        echo json_encode($TransactionType);
    }
    
    public function getTransactionTypeByIdAction() {
        $params = $this->getRequest()->getPostJson();
        
        
        $id = $this->getArrayValue('id', $params);
 
        $em = $this->getEntityManager('DefaultDb');
        $transactionTypeRepo = $em->getRepository('DefaultDb_Entities_TransactionType');
 
        $TransactionType=$transactionTypeRepo->getTransactionType($id);
        
        echo json_encode($TransactionType);
    }
    
    public function saveAction (){
        $params = $this->getRequest()->getPostJson();

        $id = $this->getArrayValue('id', $params);
        $name = $this->getArrayValue('name', $params);
        
        $em = $this->getEntityManager('DefaultDb');
        $transactionTypeRepo = $em->getRepository('DefaultDb_Entities_TransactionType');
        
        $transactionTypeRepo->addTransactionType($id,$name);
        
        $transactionType=$transactionTypeRepo->getTransactionTypeListDQL($page,$rowsPerPage,$sortField,$sortDir,            
            $id,
            $name);
        
        echo json_encode($transactionType); 
    }
    
    public function deleteAction()
    {
        $params = $this->getRequest()->getPostJson();

        $id = $this->getArrayValue('id', $params);
        
        $em = $this->getEntityManager('DefaultDb');
        $transacionTypeRepo = $em->getRepository('DefaultDb_Entities_TransactionType');
        
        $transacionTypeRepo->deleteTransactionType($id);
        
        $TransactionType=$transacionTypeRepo->getTransactionTypeListDQL($page,$rowsPerPage,$sortField,$sortDir,            
            $id);
        
        echo json_encode($TransactionType);
    }
    
}