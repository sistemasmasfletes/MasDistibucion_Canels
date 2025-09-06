<?php
$view->getJsManager()->addJs('application/OperationController/Dashboard/evalUser.js');
?>
<div class="panel panel-primary">
    <div class="panel-heading">Personal</div>
    <table class="table table-bordered table-condensed table-striped uppercase">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Tipo de usuario</th>
                <th>Evaluar</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($view->users as $user): ?>
                <tr>
                    <td><?php echo $user->getFullNameWithCode(); ?></td>
                    <td><?php echo $user->getTypeString(); ?></td>
                    <td>
                        <button type="button" class="btn btn-primary eval" user="<?php echo $user->getId(); ?>" name="<?php echo $user->getFullName(); ?>">
                            Agregar evaluación
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<div class="modal fade" id="evaluacion">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header btn-primary">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Evaluación</h4>
            </div>
            <div class="modal-body">
                <form role="form">
                    <p>
                        La evaluación que vas a ingresar para el usuario <strong id="nombreUsuario"></strong>
                        consideras que es:
                    </p>
                    <div class="radio">
                        <label>
                            <input type="radio" name="tipo" value="1">
                            Mala 
                        </label>
                    </div>
                    <div class="radio">
                        <label>
                            <input type="radio" name="tipo" value="2" id="neutral"checked>
                            Neutral
                        </label>
                    </div>
                    <div class="radio">
                        <label>
                            <input type="radio" name="tipo" value="2">
                            Buena
                        </label>
                    </div>
                    <div class="form-group">
                        <p>
                            Comentario:
                        </p>
                        <textarea class="form-control" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    Cerrar
                </button>
                <button type="button" class="btn btn-primary">
                    Agregar evaluación
                </button>
            </div>
        </div>
    </div>
</div>
