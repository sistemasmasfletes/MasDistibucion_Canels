<?php
use com\masfletes\db\DBUtil;

class OperationController_CausesController extends JController {

    private $userSessionId;

    public function init() {
        parent::init();
        if (!Model3_Auth::isAuth()) {
            $this->redirect('Index/index');
        }
        $this->userSessionId = $_SESSION['USERSESSIONID'];
    }

    public function getCausesAction() {
        $params = $this->getRequest()->getPostJson();
        $page = $this->getArrayValue('page', $params);
        $rowsPerPage = $this->getArrayValue('rowsPerPage', $params);
        $sortField = $this->getArrayValue('sortField', $params);
        $sortDir = $this->getArrayValue('sortDir', $params);
        $filter= $this->getArrayValue('filter',$params);
        $id = $this->getArrayValue('id', $filter);
        $name = $this->getArrayValue('name', $filter);
        $description = $this->getArrayValue('description',$filter);

        $em = $this->getEntityManager('DefaultDb');
        $causesRepo = $em->getRepository('DefaultDb_Entities_Causes');
        
        $status=null;
        
        $causes=$causesRepo->getCausesListDQL($page,$rowsPerPage,$sortField,$sortDir,            
            $id,
            $name,
            $description);
        
        echo json_encode($causes);
    }
    
    public function saveAction (){
        $params = $this->getRequest()->getPostJson();

        $id = $this->getArrayValue('id', $params);
        $name = $this->getArrayValue('name', $params);
        $description = $this->getArrayValue('description', $params);
        
        $em = $this->getEntityManager('DefaultDb');
        $causesRepo = $em->getRepository('DefaultDb_Entities_Causes');
        
        $causesRepo->addCauses($id,$name,$description);
        
        $causes=$causesRepo->getCausesListDQL($page,$rowsPerPage,$sortField,$sortDir,            
            $id,
            $name,
            $description);
        
        echo json_encode($causes); 
    }
    
    public function deleteAction()
    {
        $params = $this->getRequest()->getPostJson();

        $id = $this->getArrayValue('id', $params);
        
        $em = $this->getEntityManager('DefaultDb');
        $causesRepo = $em->getRepository('DefaultDb_Entities_Causes');
        
        $causesRepo->deleteCauses($id);
        
        $causes=$causesRepo->getCausesListDQL($page,$rowsPerPage,$sortField,$sortDir,            
            $id);
        
        echo json_encode($causes);
    }
    
    //FUNCIÓN CATÁLOGO DE CAUSAS EN PANTALLA DE SALVAR EVIDENCIA
    function getCausesEvidenceAction(){
        try{
            $this->hasPermission($this->getUserSessionId(), 'Route Summary', 'listar');
            
            $em = $this->getEntityManager('DefaultDb');
            $causesRepo = $em->getRepository('DefaultDb_Entities_Causes');

            $rows = $causesRepo->findAll();
            $arrCauses = array();
            foreach ($rows as $causes) {
                $arrCauses[] = array("id"=>$causes->getId(),"name"=>$causes->getName());
            }
            
            echo json_encode($arrCauses);
        }catch(Exception $ex){
            $params = array();
            $this->logAndResolveException($ex,$params);        
        }
    }
}