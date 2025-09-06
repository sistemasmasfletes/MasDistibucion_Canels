<label>Nombre Comercial:</label>
<input type="text" name="commercialName" id="commercialName" value="" />
<br/>
<label>Categoria:</label>
<select name="category" id="category" >
    <?php
    if ($view->category) :
        foreach ($view->category as $c):
            ?>
            <option value="<?php echo $c->getId(); ?>">
            <?php echo $c->getName(); ?> 
            </option> 
        <?php
        endforeach;
    endif;
    ?>
</select>
<br/>
<?php /*
<label>Ruta:</label>

<select name="route" id="route" >
    <option value="0">Seleccione Una Ruta...</option> 
    <?php
    if ($view->routes) :
        foreach ($view->routes as $r):
            ?>
            <option value="<?php echo $r->getId(); ?>">
            <?php echo $r->getName(); ?> 
            </option> 
    <?php
    endforeach;
endif;
?>
</select>

<br/>
<label>Punto de Venta:</label>
<select name="point" id="point" >
    <option value="0">Seleccione Una Ruta...</option> 
</select>
 * 
 */
?>

