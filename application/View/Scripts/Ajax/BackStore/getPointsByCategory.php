<?php /* @var $view Model3_View */ ?>
<?php 
if(is_array($view->users)):?>

    <select name="client_id">
        <?php 
        foreach($view->users as $user)
        {
            $formated = sprintf("%-20s",$user->getCommercialName() ? $user->getCommercialName() : $user->getFullName());
            echo '<option value="' . $user->getId() . '">' . $formated .'</option>';
        }
        ?>
    </select>
<?php endif;