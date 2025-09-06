<?php
if ($view->comments)
{
    ?>
    <table class="table">
        <tr>
            <th>Comentario</th>
            <th>Nuevo Estado</th>
            <th>Fecha de cambio</th>
        </tr>
        <?php foreach ($view->comments as $comment): ?>
            <tr>
                <td><?php echo $comment->getComment(); ?></td>
                <td><?php echo $comment->getNewStatus(); ?></td>
                <td><?php echo $comment->getDateChange()->format('Y-m-d'); ?></td>
            </tr>
            <?php endforeach; ?>
    </table>
<?php
}