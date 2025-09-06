<?php

class View_Helper_Display extends Model3_View_Helper
{

    function tableHelperConstructor($titles, $content, $columnNames, $ids, $idTable, $linkPagina = null, $pagina = 1, $total = 1, $status = CITA_PENDIENTE/* , $moneyColumn */)
    {
        $columns = count($titles);
        $rows = count($content);
        if (is_array($ids) && count($ids) > 0)
        {
            echo '<table id="' . $idTable . '" class="sortable reporte" >';
            echo '<tr>';
            for ($cont = 0; $cont < $columns; $cont++)
                echo '<th>' . $titles[$cont] . '</th>';
            echo '</tr>';
            for ($contRow = 0; $contRow < $rows; $contRow++)
            {
                echo '<tr id="' . $ids[$contRow] . '" class=' . ( $contRow % 2 ? 'rowEven' : 'rowOdd' ) . '>';
                for ($contColumn = 0; $contColumn < $columns; $contColumn++)
                /* if( $contColumn == $moneyColumn )
                  echo '<td>'.( '$'.number_format($content[$contRow][$columnNames[$contColumn]], 2, '.', ',')).'</td>';
                 *

                  else */
                    echo '<td style="text-align:center;">' . ( $content[$contRow][$contColumn/* $columnNames[$contColumn] */] ) . '</td>';
                echo '</tr>';
            }
            if ($linkPagina !== null) :
                ?>
                <tr>
                    <td colspan="<?php echo $columns; ?>">
                        <div id="paginas" style="text-align: center;">
                            <?php
                            if (($pagina - 1) > 0)
                                echo '<a class="pagina_' . $status . '" href="' . $linkPagina . '/' . ($pagina - 1) . '"> Anterior </a>';

                            for ($j = 1; $j <= $total; $j++)
                            {
                                if ($pagina == $j)
                                    echo "<b>" . $pagina . "</b> ";
                                else
                                    echo '<a class="pagina_' . $status . '" href="' . $linkPagina . $j . '" style="text-decoration:none"> ' . $j . ' </a>';
                            }
                            if (($pagina + 1) <= $total)
                                echo ' <a class="pagina_' . $status . '" href="' . $linkPagina . ($pagina + 1) . '"> Siguiente </a>';
                            ?>
                        </div>
                    </td>
                </tr>
            <?php endif; ?>
            <?php
            echo '</table>';
        }
    }

    function citasConstructor($citas, $actions)
    {
        $i = 0;
        $content = array();
        if (is_array($citas) && count($citas) > 0)
        {
            foreach ($citas as $cita)
            {
                $content[$i][] = $cita->nombre;
                $content[$i][] = $cita->correo;
                $content[$i][] = $cita->telefono;
                $content[$i][] = $cita->fecha_cita;
                $content[$i][] = substr($cita->hora_inicio, 0, -3);
                $content[$i][] = substr($cita->fecha_creacion, 0, -3);
                foreach ($actions as $action)
                {
                    //el valor de $action debe ser el nombre de la funcion en JS
                    $content[$i][] = '<a id="' . $action . '_' . $cita->id . '" onclick="' . $action . '(' . $cita->id . ', \'' . $cita->correo . '\')">' . $action . '</a>';
                }
                $i++;
            }
        }
        else
            $content = false;
        return $content;
    }

    function idsRowsConstructor($citas)
    {
        $ids = array();
        if (is_array($citas) && count($citas))
        {
            foreach ($citas as $cita)
            {
                $ids[] = 'cita' . $cita->id;
            }
        }
        return $ids;
    }

    public function showProduct($product)
    {
        $span = 'span-19';

        $cellsImg = '';
        $tienda = false;
        $pralImg = false;
        $catalog = false;
        $pralImghtml = '';
        $edit = false;
        $preview = false;
        $zoomAdjustX = 0;
        $zoomAdjustY = 0;
        $variantes = false;

        if ($product)
        {
            echo $this->_view->helper('Element_ElementProducto')->prepareInfoProduct($product, $this->_view, $pralImg, $pralImghtml, $cellsImg, $tienda, $zoomAdjustX, $zoomAdjustY, $variantes);
            echo '<br/>';

            echo '<div id="productsStoreData" class="' . $span . '">';
            echo $this->_view->helper('Element_ElementProducto')->buildHTMLProductClearView($product, $this->_view, $cellsImg, $zoomAdjustX, $zoomAdjustY, $variantes);            
            echo '<br />';
            echo '<div>';
            echo View_Helper_Element_ElementCatalog::buildRelatedProducts($catalog, $product);
            echo '</div>';


            echo '</div>';
        }
        else
        {
            echo '<h1> Not Found ...</h1>';
        }
    }

    public function protectedPage($pass)
    {
        $pageProtectedContent = '';
        $pageProtectedContent .= '<div id="prompterAskPassword" style="display:none" title="Contenido protegido">';
        $pageProtectedContent .= '     <strong>Introduzca la contraseña para poder ver esta página</strong><br/><br/>';
        $pageProtectedContent .= '     Contrase&ntilde;a: &nbsp;&nbsp;&nbsp;<input type="password" id="passwordContentProtected" name="passwordContentProtected">';
        $pageProtectedContent .= '     <input type="hidden" id="passwordPage" name="passwordPage" value="' . base64_decode($pass) . '">';
        $pageProtectedContent .= '</div>';

        echo $pageProtectedContent;
    }

    public function setBrowserTitle($view)
    {
        if ($view->pagina['titulo'] != '')
        {
            echo $view->pagina['titulo'];
        }
        else
        {
            if ($view->sitio['titulo_navegador'] != '')
                echo $view->sitio['titulo_navegador'];
            else
                echo $view->sitio['nombre'];

            if ($view->aditional_title === false)
                echo ' :: ' . $view->pagina['nombre'];
            else
                echo ' :: ' . $view->aditional_title;
        }
    }

    public function setDescription($view)
    {
        echo '<meta name="description" content="';

        if ($view->alternative_description === false)
            echo $view->pagina['descripcion'] != '' ? $view->pagina['descripcion'] : $view->sitio['descripcion'];
        else
            echo $view->alternative_description;
        echo '"/>';
    }

    public function protectedPageForm($view)
    {
        $nomFix = $view->sitio['nombre_fix'];
        $nomPageFix = $view->currentPag['nombre_fix'];
        $string = '';
        $string .= '<div class="wrapperFormProtected">
            <div class="wrapperFormProtected-h">
                <h3>Contenido protegido.</h3>
            </div>
            <div class="wrapperFormProtected-b">
                <b>Introduzca la contraseña para poder ver esta página</b><br/>
                <form method="post" id="formCheckPassPage" action="' . $view->linkTo('/Display/checkPassWordPageAjax/' . $nomFix . '/' . $nomPageFix . '') . '" >                
                    Contraseña: <input type="password" id="typedPass" name="typedPass" /><br/>
                    <input type="button" id="btnCheckPass" class="button" value="Aceptar" />
                    <input type="hidden" name="idPage" value="' . $view->currentPag['id'] . '" />
                    <input type="hidden" name="idSite" value="' . $view->currentPag['id_sitio'] . '" />
                </form>
            </div>
            <div class="notice" id="wrapperFormProtectedMsg" style="display:none;">No est&aacute; autorizado para ver esta p&aacute;gina</div>
            </div>';
        return $string;
    }

    public function showRoom($view, $room, $item)
    {
        $view->PrintElement()->initJCarousel(); // Quitar jcarousel
        $controller = 'Display';
        $action = 'index';
        $params[] = $view->nomFix;
        $controller = 'Display';

        $span = 'span-19';

        $cellsImg = '';
        $tienda = false;
        $product = false;
        $pralImg = false;
        $catalog = false;
        $pralImghtml = '';
        $edit = false;
        $preview = false;
        $zoomAdjustX = 0;
        $zoomAdjustY = 0;
        $variantes = false;

        $goto = ($action == 'index') ? 'inicio' : 'tienda';
        $idRoom = $room[0]['id'];
        if ($room[0]['id'])
        {
            //View_Helper_Element_ElementProducto::prepareInfoProduct($view->idProducto,$view, $view->idSitio, $view->idProducto, false, $product, $catalog, $pralImg, $pralImghtml, $cellsImg, $tienda, $zoomAdjustX, $zoomAdjustY, $variantes);
            //var_dump($view);
            View_Helper_Element_ElementReservation::prepareInfoRoom($room[0]['id'], $view, $view->idSitio, $view->id, false, $room, $pralImg, $cellsImg, $tienda, $zoomAdjustX, $zoomAdjustY);
            echo '<br/>';

            echo '<div id="productsStoreData" class="' . $span . '">';
            echo View_Helper_Element_ElementReservation::buildHTMLRoomClearView($room, $view, $pralImg, $cellsImg, $idRoom, $idRoom, false, $view->nomFix, $tienda, $item, $preview, $zoomAdjustX, $zoomAdjustY);
            $link = (isset($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : $view->makeLinks()->createLink($controller, $action, $params);
            echo '<div id="" align="right">
                    <a class="btntemplate sf-btnStyle01 ui-button ui-widget ui-corner-all ui-button-text-only" style="vertical-align: middle; margin: 0px;" role="button" aria-disabled="false" href="' . $link . '">
                        <span class="ui-button-text" style="font-size:15px">Regresar</span>
                    </a>
                  </div>';
            echo '<br />';
            echo '</div>';
        }
        else
        {
            echo '<h1> Not Found ...</h1>';
        }
    }

}
?>