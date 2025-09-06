<div class="contentBox" >
    <div class="contentBoxTop">
        <h3>Dashboard</h3>
    </div>
    <div class="innerContent" >
        <?php
        echo $view->TrHelper()->_('Total Users'). $view->totalUsers[0]['total'] . '<br/>';
        echo $view->TrHelper()->_('Registered Users').':'. $view->totalRegistered[0]['total'].':<br/><br/>';
        echo $view->TrHelper()->_('Total Meetings ') .':'. $view->totalMeetings[0]['total'] . '<br/>';
        echo $view->TrHelper()->_('Total Acepted Meetings ') .':'. $view->totalAceptedMeetings[0]['total'] . '<br/>';
        echo $view->TrHelper()->_('Total Canceled/Rejected Meetings ') .':'. $view->totalCancelMeetings[0]['total'] . '<br/>';
        echo $view->TrHelper()->_('Total Pending Meetings ') .':'. $view->totalPendingMeetings[0]['total'];        
        ?>        

        <p><label><?php echo $view->TrHelper()->_('Search');?></label><br/>
            <label class="largeInput">
                <input type="text" id="search" name="search"/>
            </label>
            <br/>
            <small><?php echo $view->TrHelper()->_('Search Name, Email or Company'); ?></small>
        </p>
        <p><input type="button" id="searchBtn" value="Search" class="smartButton"/><br/></p>

        <div id="listUser">
            <div style="text-align:center; margin: 20px;"><img src="<?php echo $view->getBaseUrl() . '/images/ajaxloader.gif' ?>" /></div>
        </div>        
    </div>
</div>
<input type="hidden" id="url-post-del" value="<?php echo $view->url(array('module' => 'EventAdmin', 'controller' => 'Users', 'action' => 'axDeleteUser')); ?>"/>
