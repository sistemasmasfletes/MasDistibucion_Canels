<?php $packs = $view->packages; ?>
<table class="table">
    <thead>
        <tr>
            <th>Embalaje</th>
            <th>Medidas(cm)</th>
            <th>Peso m&aacute;ximo(Kg)</th>
            <th>Precio unitario</th>
            <th>Unidades</th>
            <th>Eliminar</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($packs as $pack)
        {
            echo '<tr>';
            echo '<td>' . $pack->getName() . '<input type="hidden" name="idPackage[]" value="' . $pack->getId() . '"></td>';
            echo '<td>' . $pack->getWidth() . 'x' . $pack->getHeight() . 'x' . $pack->getDepth() . '</td>';
            echo '<input id="width_' . $pack->getId() . '" type="hidden" value="' . $pack->getWidth() . '"/>';
            echo '<input id="height_' . $pack->getId() . '" type="hidden" value="' . $pack->getHeight() . '"/>';
            echo '<input id="depth_' . $pack->getId() . '" type="hidden" value="' . $pack->getDepth() . '"/>';
            echo '<td>' . $pack->getWeight() . '</td>';
            echo '<td class="prices" priceoriginal="' . $pack->getPrice() . '"><span>$ ' . number_format($pack->getPrice(),2) . '</span><input type="hidden" name="packagePrice[]" value="' . $pack->getPrice() . '"/></td>';
            echo '<td>' . $pack->getUnitiBox('name="unity[]" id="unity' . $pack->getId() . '" class="boxunity span1" price="' . $pack->getPrice() . '" priceoriginal="' . $pack->getPrice() . '" pid=' . $pack->getId() . '') . '</td>';
            echo '<td><a href="#" class="deletePackage" id="' . $pack->getId() . '"><img src="' . $view->getBaseUrl() . '/images/ui/close.png" alt="close"/></a>' . '</td>';
            echo '<td><input type="text" name="total[]" id="total' . $pack->getId() . '" class="totalBoxUnity span1" value="0.00" readonly="readonly" ></td>';
            echo '</tr>';
        }
        ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="6"></td>
            <td>
                <h4>
                    <span id="sumTotal">0</span>
                </h4>
            </td>
        </tr>
        <tr>
            <td colspan="5"></td>
            <td colspan="2">
                <button id="submitButton" class="btn" type="submit">
                    Generar Pedido
                </button>
            </td>
        </tr>
    </tfoot>
</table>