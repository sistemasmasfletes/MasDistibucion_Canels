<a class="button" href="<?php echo $view->url(array('controller'=>'Index','action'=>'index'))?>"><div class="smartButton"><?php echo $view->TrHelper()->_('Back');?></div></a><br/>
<br/>
<form method="post" action="<?php echo $view->url(array('controller'=>'Index','action'=>'sendPass')); ?>">
    <label><?php echo $view->TrHelper()->_('Type your E-mail');?></label><br/>
    <input style="width: 230px;" type="text" name="email"><br/>
    <br/>
    <input class="smartButton" type="submit" value="send"/>
</form>
