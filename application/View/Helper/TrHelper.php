<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TraHelper
 *
 * @author Haydee
 */
class View_Helper_TrHelper extends Model3_View_Helper
{
    
    public function _($string)
    {
        $translate = Model3_Registry::get('translate');
        return $translate->_($string);
    }
}


?>
