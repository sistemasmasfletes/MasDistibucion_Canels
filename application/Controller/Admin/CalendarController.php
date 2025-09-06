<?php

class Admin_CalendarController extends Model3_Scaffold_Controller
{

    public function __construct($request)
    {
        $this->_sc = new Scaffold_DefaultDb_Calendar();
        parent::__construct($request);
    }

    public function init()
    {
        if (!Model3_Auth::isAuth())
        {
            $this->redirect('Index/index');
        }        
        //$this->view->getJsManager()->addJs('application/Admin/Calendar/datepickers.js');
        //$this->view->getJsManager()->addJs('bootstrap/datepicker/bootstrap-datepicker.js');
        $this->view->getJsManager()->addJs('jquery/jquery-ui-1.8.12.custom.min.js');
        $this->view->getJsManager()->addJs('application/Admin/Calendar/datepickers.js');
        //$this->view->getCssManager()->addCss('bootstrap/datepicker/bootstrap.css');
    }

    public function indexAction()
    {
        
    }

    public function addAction()
    {
        // para encriptar la contraseña en MD5
        if ($this->getRequest()->isPost())
        {
            $this->_post = $this->getRequest()->getPost();
            if (isset($this->_post['startDate']) && $this->_post['startDate'] != '')
            {
                $this->_post['startDate'] = new DateTime($this->_post['startDate']);
            }

            if (isset($this->_post['endDate']) && $this->_post['endDate'] != '')
            {
                $this->_post['endDate'] = new DateTime($this->_post['endDate']);
            }
        }

        parent::addAction();
    }

    public function editAction()
    {
        if ($this->getRequest()->isPost())
        {
            $this->_post = $this->getRequest()->getPost();
            if (isset($this->_post['startDate']) && $this->_post['startDate'] != '')
            {
                $this->_post['startDate'] = new DateTime($this->_post['startDate']);
            }

            if (isset($this->_post['endDate']) && $this->_post['endDate'] != '')
            {
                $this->_post['endDate'] = new DateTime($this->_post['endDate']);
            }
        }
        parent::editAction();
    }

}
