<?php

echo '<table class="sTable">';
echo '<thead>';
echo '<th>'.$view->TrHelper()->_('DBC').'</th>';
echo '<th>'.$view->TrHelper()->_('Firstname').'</th>';
echo '<th>'.$view->TrHelper()->_('Lastname').'</th>';
echo '<th>'.$view->TrHelper()->_('Country').'</th>';
echo '<th>'.$view->TrHelper()->_('Company').'</th>';
echo '<th>'.$view->TrHelper()->_('Profile').'</th>';
echo '<th>'.$view->TrHelper()->_('Delete').'</th>';
echo '<tbody>';
foreach ($view->users as $user)
{
    if ($user->getTypeLoginUser() != 1)
    {
        echo '<tr id="usr-container-' . $user->getId() . '">';
        echo '<td>';
        if($user->getTypeLoginUser() == 2)
                echo '<img src="'.$view->getBaseUrl().'/images/ui/icon_info.gif" alt="Registered" />';
        else
            echo '<img src="'.$view->getBaseUrl().'/images/ui/icon_inactive.gif" alt="Not registered" />';
        echo '</td>';
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
        echo '<td><a href="' . $view->url(array('module' => null, 'controller' => 'Dashboard', 'action' => 'viewProfile', 'user' => $user->getId())) . '" ><img src="'.$view->getBaseUrl().'/images/iconos/magnify.png" alt="View Profile" title="Profile"/></a></td>';
        echo '<td><a onclick="deleteUser(' . $user->getId() . ')" href="#"><img src="'.$view->getBaseUrl().'/images/iconos/user_delete.png" alt="delete" title="Delete"/></a></td>';
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
