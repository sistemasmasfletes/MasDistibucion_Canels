<?php

/*
 * Helper que imprime datos de los usuarios
 * @author H4845
 */
class View_Helper_Users extends Model3_View_Helper
{
    
    public function profileUser( $user)
    {
        if($user instanceof DefaultDb_Entities_User)
        {
        ?>
<table class="table table-striped" style="margin-bottom:0px !important">
        <body>
        <thead style="font-size: 14px; font-weight: bold;">
            <tr>
                <th style="border-bottom:0px !important;">
                    Detalles
                </th>
                
            </tr>
            
        </thead>
            <tr>
                <td style="border-right:  0px !important;">
                    Username:
                </td>
                <td style="border-left:  0px !important;">
                    <span class="icon-envelope"></span>
                    <?php echo $user->getUsername();?>
                </td>
            </tr>
            
            <tr>
                <td style="border: 0px !important;">
                    Tipo de Usuario:
                </td>
                <td style="border: 0px !important;">
                    <span class="icon-flag"></span>
                    <?php echo $user->getTypeString();?>
                </td>
            </tr>

            <tr>
                <td style="border: 0px !important;">
                    Nombre Completo:
                </td>
                <td style="border: 0px !important;">
                    <span class="icon-user"></span>
                    <?php echo $user->getFullName();?>
                </td>
            </tr>

            <tr>
                <td style="border: 0px !important;">
                    Tel&eacute;fono
                </td>
                <td style="border: 0px !important;">
                    <span class="icon-headphones"></span>
                    <?php echo $user->getLocalNumber();?>
                </td>
            </tr>

            <tr>
                <td style="border: 0px !important;">
                    Tel&eacute;fono Movil
                </td>
                <td style="border: 0px !important;">
                    <span class="icon-headphones"></span>
                    <?php echo $user->getCellPhone();?>
                </td >
            </tr>

            <tr>
                <td style="border: 0px !important;">
                    Categoria
                </td>
                <td style="border: 0px !important;">
                    <span class="icon-th-large"></span>
                    <?php   
                        $category = $user->getCategory();
                        echo ($category instanceof DefaultDb_Entities_Category)?$category->getName():'';
                   ?>
                </td>
            </tr>

            <tr>
                <td style="border: 0px !important;">
                    Punto de Recolecci&oacute;n
                </td>
                <td style="border: 0px !important;">
                    <span class="icon-home"></span>
                    <?php
                            $point = $user->getPoint();
                            echo ($point instanceof DefaultDb_Entities_Point)?$point->getAddress():'';
                   ?>
                </td>
            </tr>
        </body>
    </table>

        <?php
        }
        else
        {
            echo '<div class="alert">No se ha encontrado informacion para este usuario</div>';
        }
    }

}


?>
