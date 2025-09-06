<?php

class View_Helper_LoginHelper extends Model3_View_Helper
{

    public function login()
    {
        if (Model3_Auth::isAuth())
        {
            $identity = Model3_Auth::getCredentials();
            echo '<span class="spacer">' . $identity['username'] . '</span>';
            echo ' | <a href="' . $this->_view->url(array('module' => null, 'controller' => 'Index', 'action' => 'logout')) . '" class="spacer" >Logout</a>';
        }
        else
        {
            echo '<span class="spacer">You are not logged</span>';
        }
    }

}
