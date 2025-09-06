<?php

if ($view->result == 1)
{
    echo '<div class="successBox ">';
    echo '<div class="successBoxTop">&nbsp;';
    echo '</div>';
    echo '<div class="msgBoxContent successIcon">';
    echo $view->TrHelper()->_('Success');
    echo '</div>';
    echo '</div>';
}
else
{
    if ($view->result == 2)
    {
        echo '<div class="errorBox ">';
        echo '<div class="errorBoxTop">&nbsp;';
        echo '</div>';
        echo '<div class="msgBoxContent errorIcon">';
        echo $view->TrHelper()->_('Error: Email already exist.');
        echo '</div>';
        echo '</div>';
    }
    if($view->result === false)
    {   
        foreach ($view->errors as $error)
        {
            echo '<div class="errorBox ">';
            echo '<div class="errorBoxTop">&nbsp;';
            echo '</div>';
            echo '<div class="msgBoxContent errorIcon">';
            echo '<p>' .$view->TrHelper()->_($error) . '</p>';
            echo '</div>';
            echo '</div>';
        }
    }
    
}