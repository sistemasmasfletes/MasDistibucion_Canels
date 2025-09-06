<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
*/

/**
 * 
 *
 * @author Alopez
 */
class View_Helper_Catalogos extends Model3_View_Helper
{   
    public function printReport($catalogos,$totalPages)
    {        
        ?>
        <!--div class="catContainer"-->
        <?php if(is_array($catalogos) && count($catalogos) > 0):?>
        <div class="catContainer">
            <table class="zebraTable" style="margin:0px;">
                <tr>
                    <th>T&iacute;tulo</th>
                    <th style="text-align: center;">Productos</th>
                    <th style="text-align: center;">Venta</th>
                    <th style="text-align: center;">Editar</th>
                    <th style="text-align: center;">Eliminar</th>
                </tr>
                <?php $indx = 0;?>
                <?php foreach($catalogos as $catalogo):?>
                <?php $class = $indx++ % 2 == 0 ? 'zebraodd':'zebraeven'?>
                    <tr class="<?php echo $class;?>">
                            <?php if($catalogo->id_padre ==0): ?>
                                <td> <?php echo $catalogo->titulo;?></td>
                            <?php else: ?>
                                <td><span class="sf-subCatalogTD"><img alt="pertenencia" src="<?php echo $this->_view->getBaseUrl().'/images/ui/union.png' ?>"/> <?php echo $catalogo->titulo;?></span></td>
                            <?php endif; ?>
                        <td style="text-align: center;"> <?php echo $catalogo->total_productos;?></td>
                        <td style="text-align: center;"><?php echo $catalogo->venta == 1 ? 'Si':'No';?></td>
                        <td style="text-align: center;"><a class="edit" id="<?php echo $catalogo->id;?>">Editar</a></td>
                        <td style="text-align: center;"><a class="delete" id="delete-<?php echo $catalogo->id;?>">Eliminar</a></td>
                    </tr>
                <?php endforeach;?>
            </table>
        </div>
        <div id="paginas">
            <?php if($totalPages > 1):?>
                <?php for($i = 1; $i<=$totalPages;$i++):?>
                    <a id="page-<?php echo $i;?>" class="page"><?php echo $i;?></a> &nbsp;
                <?php endfor;?>
            <?php endif;?>
        </div>
        <?php endif;?>
     <?php
    }

    protected function printLinkDialog($paginas)
    {
    ?>
        <table>
            <tr>
                <td><input id="firstRadioLink" name="radioLink" class="radioLink" type="radio" checked="checked" value="0"/><label>Enlace externo o e-mail:</label></td>
            </tr>
            <tr>
                <td style="padding-left: 5px"><input class="input300" type="text" id="linkExterna" value=""/></td>
            </tr>
            <tr>
                <td><input name="radioLink" class="radioLink" type="radio" value="1"/><label>Interna: </label></td>
            </tr>
            <tr>
                <td style="padding-left: 5px">
                    <div>
                        <select id="linkInterna" class="input300" disabled>
                            <?php
                            if(is_array($paginas) && count($paginas) > 0)
                            {
                                foreach($paginas as $pagina) :
                                    $namePage = ($pagina['id_padre'] == 0) ? '-&nbsp;' : '&nbsp;&nbsp;+&nbsp;&nbsp;';
                                    $namePage .= $pagina['nombre'];
                                ?>
                                    <option value="<?php echo $pagina['id']; ?>"><?php echo $namePage; ?></option>
                                <?php
                                endforeach;
                            }
                            ?>
                        </select>
                    </div>
                </td>
            </tr>
        </table>
    <?php
    }
}