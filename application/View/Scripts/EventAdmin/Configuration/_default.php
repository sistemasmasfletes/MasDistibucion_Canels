<div class="contentBox" >
    <div style="display: none" id="message" >
        <div class="successBox" id="rounded" style="text-align: center">
            <div class="successBoxTop"></div>
            <div class="msgBoxContent successIcon">
                <?php echo $view->TrHelper()->_('Changed Save'); ?>
            </div>
        </div>
    </div>
    <div class="contentBoxTop">
        <h3><?php echo $view->TrHelper()->_('Configuration'); ?></h3>
    </div>
    <div class="innerContent" >
        <input type="hidden" id="url" name="url" value="<?php echo $view->url(array('module' => 'EventAdmin', 'controller' => 'Configuration', 'action' => 'axSaveConfiguration')) ?>"/>
        <div>
            <form id="formWhereAndWhen">
                <?php echo $view->TrHelper()->_('Where and when');?>:<br/><br/>
                <textarea class="textWhereAndWhen" name="textWhereAndWhen" id="textWhereAndWhen"><?php
                    if ($view->textWhereAndWhen)
                    {
                        echo $view->textWhereAndWhen;
                    }
                ?></textarea>
                <br/><br/>
                <input id="submitWhereAndWhen" class="smartButton" value="Save"/>
            </form> 
        </div>
    </div>
</div>
