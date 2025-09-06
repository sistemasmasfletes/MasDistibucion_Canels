<?php
if ($view->branches)
{
    ?>

 
    <table class="table table-striped">
        <thead>
             <tr style="border-bottom: 1px solid #998c73 !important;background-color: #eac3a0 !important;">
        <th style="border: 0px !important">Sucursales</th>
        <th style="border: 0px !important" ></th>
        <th style="border: 0px !important" ></th>
        <th style="border: 0px !important" ></th>
    </tr>
            <tr>
                <th>Nombre</th>
                <th>Direccion</th>
                <th>Punto de recoleci&oacute;n </th>
                <th style="border-left: 0px !important"></th>
            </tr>
        </thead>

        <tbody>
            <?php
            foreach ($view->branches as $branches)
            {
                $addPoint = '<div class="btn-group"><a href="#" class="pers-btn icono-camion icono-tam-tabla  addPoint" id="' . $branches->getId() . '" title="Asignar punto de recolección"> </a></div>';
                $delPoint = '<div class="btn-group"><a href="#" class="pers-btn icono-camion icono-tam-tabla  unsetPoint" id="'.$branches->getId().'" title="Desvincular punto de recolección">  </a></div>';
                ?>
                <tr>
                    <td style="border:  0px !important;"><?php echo $branches->getName(); ?></td>
                    <td style="border:  0px !important;"><?php echo $branches->getDirection(); ?></td>
                    <td style="border:  0px !important;"><?php echo $branches->getPoint() ? $branches->getPoint()->getFullAddress() : ""; ?></td>
                    <td style="border:  0px !important;">
                        <?php if($branches->getPoint())                            
                            echo $delPoint;
                        else 
                            echo $addPoint;
                        ?>
                    </td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
    <?php
}
else
{
    echo '<div class="alert">No se encontro informacion para este usuario</div>';
}
?>

    <div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h3 id="myModalLabel">Modal header</h3>
        </div>
        <div class="modal-body">
            <?php $view->Orders()->printSearchClients();?>
        <!--
        <?php if ($view->points):
                foreach ($view->points as $point): ?>
                    <input type="radio" name="points" class="pointOption" value="<?php echo $point->getId(); ?>"><?php echo $point->getName(); ?><br>
        <?php   endforeach; 
            else: ?>
                <div class="alert">No tiene ningun punto a asignar</div>
        <?php endif; ?>
        -->
        </div>
        <div class="modal-footer">
            <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
            <button class="btn btn-primary" id="saveChange">Save changes</button>
        </div>
    </div>