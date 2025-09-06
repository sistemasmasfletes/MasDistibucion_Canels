<div  class="rightBoxes">
    <div class="rightBoxesTop"><h3><?php echo $view->TrHelper()->_('Where and When');?></h3></div>
     <div class="rightContent" style="text-align: center;">  
         <?php 
         $sidebar = new View_Helper_SidebarHelper();
         echo $sidebar->getWhereAndWhen();
         ?>
     </div>
</div>
