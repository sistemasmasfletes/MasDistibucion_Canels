<?php /* @var $view Model3_View */ ?>
<div class="contentBox">
    <div class="contentBoxTop">
        <h3><?php echo $view->TrHelper()->_('Users Management');?></h3>
    </div>
    <div class="innerContent">        
        <h4><?php echo $view->TrHelper()->_('Import users'); ?></h4>
        <br/>
        <a target="_blank" href="<?php echo $view->getBaseUrlPublic().'/material/users.csv' ?>"><?php echo $view->TrHelper()->_('Sample file')?></a>
        <br/>
        <form enctype="multipart/form-data" id="bdc-import-form" method="post" action="<?php echo $view->url(array('module'=>'EventAdmin','controller'=>'Users','action'=>'import')); ?>">
            <label for="csv_file"><?php echo $view->TrHelper()->_('Your .csv file : ');?> </label>
            <input type="file" name="csv_file" id="csv_file"/>
            <i><?php echo $view->TrHelper()->_('(Only upload a .csv file, with a maximum size of 8 MB'); ?></i>
            <br/>
            <input type="submit" value="Import" />        
        </form>
        <br/>
        <br/>
        <h4><?php echo $view->TrHelper()->_('Add a user');?></h4>        
        <form method="post" action="<?php echo $view->url(array('module' => 'EventAdmin', 'controller' => 'Users', 'action' => 'addUser')); ?>">
            <table>
                <tr>
                    <td>
                        <label for="userMail"><?php echo $view->TrHelper()->_('E-mail');?> : </label>
                    </td>
                    <td>
                        <input type="text" name="userMail" />        
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="userFirstName"><?php echo $view->TrHelper()->_('First name'); ?>: </label>
                    </td>
                    <td>
                        <input type="text" name="userFirstName" />
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="userLastName"><?php echo $view->TrHelper()->_('Last name');?> : </label>
                    </td>
                    <td>
                        <input type="text" name="userLastName" />
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="userCompany"><?php echo $view->TrHelper()->_('Company'); ?>: </label>
                    </td>
                    <td>            
                        <input type="text" name="userCompany" />    
                    </td>
                </tr>            
                <tr>
                    <td colspan="2" >* <?php echo $view->TrHelper()->_('Required field');?></td>
                </tr>
                <tr>
                    <td colspan="2" ><input type="submit" value="Add" /></td>
                </tr>
            </table>
        </form>
    </div>
</div>
<input type="hidden" id="bdc-ajax-url" value="<?php echo $view->url(array('module' => 'EventAdmin', 'controller' => 'Users', 'action' => 'axImport')); ?>"/>
