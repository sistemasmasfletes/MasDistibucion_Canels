<?php
echo '<table class="sTable">';
echo '<thead>';
echo '<th>'.$view->TrHelper()->_('Firstname').'</th>';
echo '<th>'.$view->TrHelper()->_('Lastname').'</th>';
echo '<th>'.$view->TrHelper()->_('Country').'</th>';
echo '<th>'.$view->TrHelper()->_('Company').'</th>';
echo '<th>'.$view->TrHelper()->_('Profile').'</th>';
echo '<th>'.$view->TrHelper()->_('Message').'</th>';
echo '<th>'.$view->TrHelper()->_('Invite').'</th>';
echo '<th>'.$view->TrHelper()->_('Favorite').'</th>';
echo '<tbody>';
foreach ($view->users as $user)
{
    if ($view->user1->getId() != $user->getId())
    {
        echo '<tr>';
        echo '<td>' . htmlentities($user->getFirstName()) . '</td>';
        echo '<td>' . htmlentities($user->getLastName()) . '</td>';
        if($user->getCountry())
            echo '<td>' . htmlentities($user->getCountry()->getInitialsCountry()) . '</td>';
        else
            echo '<td class="secondCol"></td>';
        if (strlen($user->getCompanyUrl()) > 0)
            echo '<td class="secondCol"><a target="_blank" href="http://' . $user->getCompanyUrl() . '">' . htmlentities($user->getCompany()) . '</a></td>';
        else
            echo '<td class="secondCol">' . htmlentities($user->getCompany()) . '</td>';
        echo '<td><a href="' . $view->url(array('module' => null, 'controller' => 'Dashboard', 'action' => 'viewProfile', 'user' => $user->getId())) . '" ><img src="'.$view->getBaseUrl().'/images/iconos/magnify.png" alt="View Profile" title="Profile" /></a></td>';
        echo '<td><a href="' . $view->url(array('module' => null, 'controller' => 'Message', 'action' => 'sendMessage', 'user' => $user->getId())) . '" ><img src="'.$view->getBaseUrl().'/images/iconos/email.png" alt="Message" title="Message" /></a></td>';
        if ($user->getAllowRequests())
            echo '<td><a href="' . $view->url(array('module' => null, 'controller' => 'Calendar', 'action' => 'invite', 'user' => $user->getId())) . '" ><img src="'.$view->getBaseUrl().'/images/iconos/calendar.png" alt="Invite" title="Invite" /></a></td>';
        else
            echo'<td></td>';       
        if (count($view->users1) > 0)
        {
            $mark = false;
            foreach ($view->users1 as $users1)
            {
                if ($user->getId() == $users1->getId())
                {
                    echo '<td><img src="'.$view->getBaseUrl().'/images/ui/star.png" alt="Favorite" /></td>';
                    $mark = true;
                }
            }
            if ($mark == false)
                echo '<td><img class="addFav" id="id_'.$user->getId().'" src="'.$view->getBaseUrl().'/images/ui/star_add.png" alt="Add" style="cursor:pointer;" /></td>';
        }
        else
            echo '<td><img class="addFav" id="id_'.$user->getId().'" src="'.$view->getBaseUrl().'/images/ui/star_add.png" alt="Add" style="cursor:pointer;" /></td>';
        echo '</tr>';
    }
}
echo '</tbody>';
echo '</table>';

echo '<div id="paginator">';
echo 'Page: ';
for ($i = 0; $i <= $view->totalPages - 1; $i++)
{
    if ($i + 1 == $view->current)
        echo ($i + 1) . ' ';
    else
        echo '<a class="pagLink" id="page_' . ($i + 1) . '" >' . ($i + 1) . '</a> ';
}
echo '</div>';
?>
