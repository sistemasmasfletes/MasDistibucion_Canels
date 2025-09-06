<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of RolseSystem
 *
 * @author el Guerra
 */
class Plugin_RoleSystem
{
     /**
     *
     * @param Model3_Request $request
     */
    public function onPreDispatch($request)
    {
//        $deniedController = 'Index';
//        $deniedAction = 'logout';
//
//        if ($request->isModule())
//        {
//            if (Model3_Auth::isAuth())
//            {
//                $acl = new Model3_Acl();
//                $module = $request->getModule();
//                switch ($module)
//                {
//                    case 'Admin':
//                        $acl->allowGroups(DefaultDb_Entities_User::USER_ADMIN);
//                        break;
//                    default:
//                        $acl->allowGroups(DefaultDb_Entities_User::USER_COMMON);
//                        break;
//                }
//
//                $credentials = Model3_Auth::getCredentials();
//                if ($acl->isAllowed($credentials['id'], @$credentials['type_login_user']))
//                {
//                    // Autorizado
//                    return;
//                }
//            }
//            $request->setModule(null);
//            $request->setController($deniedController);
//            $request->setAction($deniedAction);
//        }
//        else
//        {
//            if ($request->getController() == 'Index' && $request->getAction() == 'cambiaPass')
//                if (!Model3_Auth::isAuth())
//                {
//                    $request->setModule(null);
//                    $request->setController($deniedController);
//                    $request->setAction($deniedAction);
//                }
//        }
    }
    
    public function onPostDispatch()
    {}
}

?>
