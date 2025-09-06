<div class="container">
    <div class="row" >
        <div class="span12 blockGray">
            <div class="blockInner">
                <h1>Alta de Usuarios</h1>
                <?php
                echo '<a class="btn" href="' . $view->url(array('action' => 'index')) . '" >Regresar</a>';
                echo '<div class="clear" style="height:15px;" ></div>';
                if($view->result === true)
                {
                    echo '<div class="alert alert-success">';
                    echo 'Registro insertado con exito';
                    echo '</div>';
                }
                else if($view->result === 2)
                {
                    echo '<div class="alert alert-error">';
                    echo 'El nombre de usuario ya existe, intente otro nombre de usuario';
                    echo '</div>';
                }
                else if($view->result === 3)
                {
                    echo '<div class="alert alert-error">';
                    echo 'La clave de usuario ya existe, intente otra clave de usuario';
                    echo '</div>';
                }
                ?>
                <form name="user" method="post" action="<?php $view->url(array('module' => 'Admin','controller' => 'AdminUsers', 'action' => 'add'));?>">
                    <label>Clave:</label>
                    <input type="text" name="code" id="code" required="required" />
                    <br/>
                    <label>Nombre:</label>
                    <input type="text" name="firstName" id="firstName" required="required"/>
                    <br/>
                    <label>Apellidos:</label>
                    <input type="text" name="lastName" id="lastName" />
                    <br/>
                    <label>Usuario:</label>
                    <input type="text" name="username" id="username" required="required"/>
                    <br/>
                    <label>Password:</label>
                    <input type="password" name="password" id="password" required="required"/>
                    <br/>
                    <label>Tipo de Usuario:</label>
                    <select name="type" id="type" >
                        <option value="0" selected="select">Seleccione...</option>
                        <option value="<?php echo DefaultDb_Entities_User::USER_ADMIN?>">Administrador</option>
                        <option value="<?php echo DefaultDb_Entities_User::USER_DRIVER?>">Conductor</option>
                        <option value="<?php echo DefaultDb_Entities_User::USER_CLIENT?>">Cliente</option>
                        <option value="<?php echo DefaultDb_Entities_User::USER_SECRETARY?>">Secretaria</option>
                    </select>
                    <br/>
                    <div id="client" >
                    </div>
                    <br/>
                    <input type="hidden" name="dayInvoice" id="dayInvoice" value="<?php echo DefaultDb_Entities_User::DAY_MONDAY?>"/>
                    <input type="submit" value="Guardar" class="bottom"/>
                </form>
            </div>
        </div>
    </div>
</div>
