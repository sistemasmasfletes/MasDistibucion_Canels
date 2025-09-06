<div class="container">
    <div class="row" >
        <div class="span12 blockGray">
            <div class="blockInner">
                <h1>Asignación de chofer y vehículo</h1>
                <a class="btn" href=" <?php echo $view->url(array('action' => 'index')); ?>" >Regresar</a>
                <div class="clear" style="height:15px;" ></div>
                <?php if($view->res == true): ?>
                    <div class="alert alert-success">
                    Registro asignado con exito
                    </div>
                <?php endif; ?>
                <form method="post" >
                    <label>Chofer</label>
                    <select name="user" >
                        <?php
                        foreach($view->users as $user)
                        {
                            echo '<option value="'.$user->getId().'">'.$user->getFirstName().'</a>';
                        }
                        ?>
                    </select>
                    <label>Vehiculo</label>
                    <select name="vehicle" >
                        <?php
                        foreach($view->vehicles as $vehicle)
                        {
                            echo '<option value="'.$vehicle->getId().'">'.$vehicle->getName().'</a>';
                        }
                        ?>
                    </select>
                    <br/>
                    <button type="submit" class="btn">Asignar</button>
                </form>
            </div>
        </div>
    </div>
</div>
