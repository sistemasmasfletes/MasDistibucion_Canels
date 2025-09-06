<?php if ($view->invoicesUsers): ?>
    <div class="alert alert-success">
        Se han realizado correctamente <?php echo count($view->invoicesUsers); ?> facturas 
    </div>
<?php else: ?>
    <div class="alert alert-error">
        No hay reporte para este lapso de tiempo
    </div>
<?php endif; ?>