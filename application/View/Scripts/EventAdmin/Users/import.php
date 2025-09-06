<?php

if($view->error)
{
    $class = "errorBox";
    $icon = "errorIcon";
    $msg = $view->msgError;    
        
}
else
{
    $class = "successBox";
    $icon = "successIcon";
    $msg = 'Success!!';
}

if(count($view->messages) > 0)
{
    $msg .= '<br/><br/><strong>'.$view->TrHelper()->_('Warnings').' : </strong>';
    foreach($view->messages as $message)
    {
        $msg .= '<br/>'.$view->TrHelper()->_($message);
    } 
}
        
echo '<div class="'.$class.'"><div class="msgBoxContent '.$icon.'">';
    echo '<p>'.$msg.'</p>';
    echo '<a href="'.$view->url(array('module'=>'EventAdmin','controller'=>'Users','action'=>'index')).'">Back</a>';
echo '</div></div>';
    