<img alt="event_banner" title="AWC 2012" src="<?php echo $view->getBaseUrlPublic(); ?>/images/banners/awc2012Banner.jpg" style="margin-bottom: 15px;" />
<form id="contactUs" method="post" action="contactUs">
    <table class="sTable" style="float: left;">
        <tr><td width="25%"><?php echo $view->TrHelper()->_('First Name'); ?>* </td><td width="45%"><input class="mediumInput" id="firstName" name="firstName" type="text"/></td><td class="left"></td></tr>
        <tr class="oddRow"><td><?php echo $view->TrHelper()->_('Last Name'); ?>* </td><td><input class="mediumInput" id="lastName" name="lastName" type="text" /></td><td class="left"></td></tr>
        <tr><td><?php echo $view->TrHelper()->_('E-mail'); ?>*</td><td><input class="mediumInput" id="username" name="username" type="text" /></td><td class="left"></td></tr>
        <tr class="oddRow"><td><?php $view->TrHelper()->_('Phone'); ?></td><td><input class="mediumInput" id="telephone" name="telephone" type="text" /></td><td class="left"></td></tr>
        <tr><td><?php echo $view->TrHelper()->_('Event Name'); ?></td><td><input class="mediumInput" id="eventName" name="eventName" type="text" /></td><td class="left"></td></tr>
        <tr class="oddRow"><td><?php echo $view->TrHelper()->_('Event Website'); ?></td><td><input class="mediumInput" id="eventWebsite" name="eventWebsite" type="text"/></td><td class="left"></td></tr>
        <tr><td><?php echo $view->TrHelper()->_('Comments'); ?></td><td><textarea class="mediumInput" id="comments" name="comments"></textarea></td><td class="left"></td></tr>
        <tr><td></td><td><input class="submit_img" value="" style="text-align: center;" type="submit"/></td><td class="left"></td></tr>
    </table><br/><br/>
</form>  