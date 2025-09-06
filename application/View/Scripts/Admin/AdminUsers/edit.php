<?php $user = $view->user; ?>
<div class="container">
    <div class="row" >
        <div class="span12 blockGray">
            <div class="blockInner">
                <h1>Edici&oacute;n de Usuarios</h1>
                <?php
                echo '<a class="btn" href="' . $view->url(array('action' => 'index')) . '" >Regresar</a>';
                echo '<div class="clear" style="height:15px;" ></div>';
                if ($view->result === true)
                {
                    echo '<div class="alert alert-success">';
                    echo 'Registro modificado con &eacute;xito';
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
                <form name="user" method="post" action="<?php $view->url(array('module' => 'Admin', 'controller' => 'AdminUsers', 'action' => 'add')); ?>">
                    <label>Clave:</label>
                    <input required="required" type="text" name="code" id="code" value="<?php echo $clave = ($user->getCode() != "") ? $user->getCode() : "" ?>" />
                    <br/>
                    <label>Nombre:</label>
                    <input required="required" type="text" name="firstName" id="firstName" value="<?php echo $firstName = ($user->getFirstName() != "") ? $user->getFirstName() : "" ?>" />
                    <br/>
                    <label>Apellidos:</label>
                    <input type="text" name="lastName" id="lastName" value="<?php echo $lastName = ($user->getLastName() != "") ? $user->getLastName() : "" ?>" />
                    <br/>
                    <label>Usuario:</label>
                    <input type="text" name="username" required="required" id="username" value="<?php echo $username = ($user->getUsername() != "") ? $user->getUsername() : "" ?>" />
                    <br/>
                    <label>Password:</label>
                    <input type="password" name="password" required="required" id="password" value="<?php echo $pass = ($user->getPassword() != "") ? $user->getPassword() : "" ?>" />
                    <br/>    
                    <?php if($user->getType() == 0 ||$user->getType() == DefaultDb_Entities_User::USER_CLIENT ) : ?>
                    <label>Nombre Comercial:</label>
                    <input type="text" name="commercialName" id="commercialName" value="<?php echo $nComm = ($user->getCommercialName() != "") ? $user->getCommercialName() : "" ?>" />
                    <br/>
                    <label>Categoria:</label>
                    <select name="category" id="category" >
                        <?php
                        if ($view->category) :
                            foreach ($view->category as $c):
                                $selected = $user->getCategory() &&($user->getCategory()->getId() == $c->getId())? 'selected = "selected"' : ""?>
                                <option value="<?php echo $c->getId(); ?>" <?php echo $selected; ?>>
                                    <?php echo $c->getName(); ?> 
                                </option> 
                                <?php
                            endforeach;
                        endif;
                        ?>
                    </select>
                    <br/>
                    
                    <br/>
                    <?php 
                    /*
                    <label>Sucursales (Puntos de venta):</label>
                    <?php  // Solo mostrarlos en edicion
                    $branches = $user->getBranches();
                    foreach ($branches as $branch) 
                    {
                        echo 'Nombre sucursal: <strong>'.$branch->getName().'</strong> Dirección:<strong>'.$branch->getDirection().'</strong><br/>';
                        ?>
                    Ruta:
                    <select name="route[]" id="route_<?php echo $branch->getId();?>" class="route" >
                    <?php
                        if ($view->routes) :
                            foreach ($view->routes as $r): 
                            $selected = ($view->rootOfUser == $r) ? 'selected = "selected"' : ""?>
                                <option value="<?php echo $r->getId(); ?>" <?php echo $selected; ?>>
                                    <?php echo $r->getName(); ?> 
                                </option> 
                                <?php
                            endforeach;
                        endif;
                        ?>
                    </select>
                    &nbsp;Punto de venta:
                        <select name="point[]" id="point_<?php echo $branch->getId();?>" class="point" >
                         <?php if($branch->getPoint()===null)
                        {
                            echo '<option value="0" selected="selected">Seleccione opción</option>';
                        }
                        foreach($view->pointOfRoot as $p):
                            
                        $selected = ( $branch->getPoint() && $branch->getPoint()->getId() == $p->getPoint()->getId()) ? 'selected = "selected"' : "";
                        
                        ?>
                            <option value="<?php echo $p->getPoint()->getId(); ?>"<?php echo $selected; ?>>
                            <?php echo $p->getPoint()->getName();
                                  echo $descripPoint = ($p->getPoint()->getType() == DefaultDb_Entities_Point::TYPE_EXCHANGE_CENTER) ? ' (Centro de Intercambio)':' (Punto de Venta)';?>
                        </option>
                    <?php endforeach;?>
                    </select>
                    <input type="hidden" value="<?php echo $branch->getId();?>" name="idroutes[]" />
                    <br/>
                    <?php
                    }
                     * 
                     */
                    ?>
                    <?php endif; ?>
                    <div id="client" >
                    </div>
                    <br/>
                    <input type="hidden" name="dayInvoice" id="dayInvoice" value="<?php echo DefaultDb_Entities_User::DAY_MONDAY ?>"/>
                    <input type="submit" value="Guardar" class="bottom"/>
                </form>
            </div>
        </div>
    </div>
</div>
