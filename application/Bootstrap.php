<?php

class Bootstrap extends Model3_Site_Bootstrap
{

    public function _initBase()
    {
        session_start();
    }

    public function _initEngine()
    {
        Model3_Site::registerModule('Admin');
        Model3_Site::registerModule('User');
        Model3_Site::registerModule('Driver');
        Model3_Site::registerModule('Warehouseman');
        Model3_Site::registerModule('Ajax');
        Model3_Site::registerModule('OperationController');
        Model3_Site::registerPlugin('roles', new Plugin_RoleSystem());
        Model3_Site::registerPlugin('translate', new Plugin_Translate());
    }
}