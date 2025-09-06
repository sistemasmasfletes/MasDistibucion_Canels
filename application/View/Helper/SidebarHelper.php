<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SidebarHelper
 *
 * @author usuario
 */
class View_Helper_SidebarHelper extends Model3_View_Helper
{
    public function __construct()
    {
        
    }
    
    public function getWhereAndWhen()
    {
        $credentials = Model3_Auth::getCredentials();
        $dbs = Model3_Registry::getInstance()->get('databases');
        $em = $dbs['DefaultDb'];
        /* @var $em Doctrine\ORM\EntityManager */

        $whereAndWhen = $em->getRepository('DefaultDb_Entities_Configuration')->countAllWhereAndWhen();
        $textWhereAndWhen = $em->getRepository('DefaultDb_Entities_Configuration')->getTextWhereAndWhen($whereAndWhen[0]['total']);
        if( is_array($textWhereAndWhen) && count($textWhereAndWhen)>0)
        {
            $textWhereAndWhen = $textWhereAndWhen[0]->getWhereAndWhen();
        }
        else
            $textWhereAndWhen = null;
        ?>

        <div>
              <?php echo $textWhereAndWhen;?>
        </div>
        <?php
    }
}

?>
