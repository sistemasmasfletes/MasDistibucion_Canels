<div class="contiene-bread">
    <ol class="breadcrumb">
        <li class="active">Mis Datos</li>
        <li class="active actualpg ">Datos Fiscales de la Empresa</li>
    </ol>
</div>
<div class="container">
    <div class="row" >
        <!--<div class="span12 blockGray">-->
        <div class="blockInner">
            <?php

            //var_dump($_SESSION['isapp']);
            
            if ($view->resp === true) {
                echo '<div class="alert alert-success">';
                echo 'Tu perfil ha sido modificado correctamente';
                echo '</div>';
            } else if ($view->error != false) {
                echo '<div class="alert alert-error">';
                echo $view->error;
                echo '</div>';
            }
            ?>
            <span style="font-size:1.5em; font-weight:bold">Perfil: estos son los datos comerciales en caso de ser un negocio</span>
            <div class="clear ui-widget ui-widget-content ui-corner-bottom">
                <br/>
                <form id="formProfileUser" method="post">
                    <table style="width:100%" >
                        <div class="" >
							<tr>
                                <th style="width: 20%">Nombre Comercial:</th>
                                <td style="width: 30%"><input type="text" id="commercialName" name="commercialName" value="<?php echo $view->user->getCommercialName() != "" ? $view->user->getCommercialName() : ""; ?>"/></td>
                                <th style="width: 20%">Banco:</th>
                                <td style="width: 30%"><input type="text" id="bank" name="bank" value="<?php echo $view->user->getBank() != "" ? $view->user->getBank() : ""; ?>"/></td>
                                
                            </tr>
                            <tr>
                                <th>Raz&oacute;n Social:</th>
                                <td><input type="text" id="businessName" name="businessName" value="<?php echo $view->user->getBusinessName() != "" ? $view->user->getBusinessName() : ""; ?>"/></td>
                                <th>Cuenta Bancaria:</th>
                                <td><input type="text" id="account" name="account" value="<?php echo $view->user->getAccount() != "" ? $view->user->getAccount() : ""; ?>"/></td>
                           	</tr>
                            <tr>
                                <th>RFC:</th>
                                <td><input type="text" id="rfc" name="rfc" value="<?php echo $view->user->getRfc() != "" ? $view->user->getRfc() : ""; ?>"/></td>
                                 <th>Clabe:</th>
                                <td><input type="text" id="clabe" name="clabe" value="<?php echo $view->user->getClabe() != "" ? $view->user->getClabe() : ""; ?>"/></td>
                           	</tr>                            <!-- tr>
                                <th>Dia de Facturacion:</th>
                                <td><select name="dateInvoice" id="dateInvoice">
                                        <?php /* $selected = ($view->user->getDayInvoice() == DefaultDb_Entities_User::DAY_MONDAY ) ? "selected = 'selected'" : ""; ?>
                                        <option value="<?php echo DefaultDb_Entities_User::DAY_MONDAY ?>" <?php echo $selected; ?>>Lunes</option>
                                        <?php $selected = ($view->user->getDayInvoice() == DefaultDb_Entities_User::DAY_TUESDAY ) ? "selected = 'selected'" : ""; ?>
                                        <option value="<?php echo DefaultDb_Entities_User::DAY_TUESDAY ?>" <?php echo $selected; ?>>Martes</option>
                                        <?php $selected = ($view->user->getDayInvoice() == DefaultDb_Entities_User::DAY_WEDNESDAY ) ? "selected = 'selected'" : ""; ?>
                                        <option value="<?php echo DefaultDb_Entities_User::DAY_WEDNESDAY ?>" <?php echo $selected; ?>>Miercoles</option>
                                        <?php $selected = ($view->user->getDayInvoice() == DefaultDb_Entities_User::DAY_THURSDAY ) ? "selected = 'selected'" : ""; ?>
                                        <option value="<?php echo DefaultDb_Entities_User::DAY_THURSDAY ?>" <?php echo $selected; ?>>Jueves</option>
                                        <?php $selected = ($view->user->getDayInvoice() == DefaultDb_Entities_User::DAY_FRIDAY ) ? "selected = 'selected'" : ""; ?>
                                        <option value="<?php echo DefaultDb_Entities_User::DAY_FRIDAY ?>" <?php echo $selected; ?>>Viernes</option>
                                        <?php $selected = ($view->user->getDayInvoice() == DefaultDb_Entities_User::DAY_SATURDAR ) ? "selected = 'selected'" : ""; ?>
                                        <option value="<?php echo DefaultDb_Entities_User::DAY_SATURDAR ?>" <?php echo $selected; ?>>Sabado</option>
                                        <?php $selected = ($view->user->getDayInvoice() == DefaultDb_Entities_User::DAY_SUNDAY ) ? "selected = 'selected'" : ""; ?>
                                        <option value="<?php echo DefaultDb_Entities_User::DAY_SUNDAY ?>" <?php echo $selected; */?>>Domingo</option>
                                    </select></td>
                                <td></td>
                            </tr-->
                        </div>
                        <div class="">
                            <tr>
                                <th>País:</th>
                                <td><select name="country" id="country" onchange="getStates(this.value)">
                                        <option value="">Seleccionar país</option>
                                        <?php 
                                        foreach($view->countries as $pais){
                                            $seleccion = $view->user->getCountry() != NULL ? ($view->user->getCountry()->getId() == $pais->getId() ? 'selected = "selected"':'') :'';
                                            echo '<option value="'.$pais->getId().'" '.$seleccion.'>'.$pais->getNombre().'</option>';
                                        }
                                        ?>
                                    </select>
                                </td>
                                 <th>Clabe:</th>
                                <td><input type="text" id="clabe" name="clabe" value="<?php echo $view->user->getClabe() != "" ? $view->user->getClabe() : ""; ?>"/></td>
                            </tr>
                            
                            <tr>
                                <th>Estado:</th>
                                <td><select name="state" id="state" onchange="getCities(this.value)">
                                        <option value="">Seleccionar estado</option>
                                        <?php
                                        foreach($view->states as $state){
                                            $seleccion = $view->user->getState() != NULL ? ( $view->user->getState()->getId() == $state->getId() ? 'selected = "selected"':'') : '';
                                            echo '<option value="'.$state->getId().'" '.$seleccion.'>'.$state->getName().'</option>';
                                        }
                                            ?>
                                    </select>
                              	</td>
                                <th>Correo Electr&oacute;nico:</th>
                              	<td><input type="text" id="emial" name="email" value="<?php echo $view->user->getMail() != "" ? $view->user->getMail() : ""; ?>"/></td>
                            </tr>
                            
                            <tr>
                                <th>Ciudad:</th>
                                <td><select name="city" id="city" onchange="">
                                        <option value="">Seleccionar ciudad</option>
                                        <?php
                                        if ($view->cities != null) {
                                            foreach ($view->cities as $city) {
                                                $seleccion = $view->user->getCity() != NULL ? ($view->user->getCity()->getId() == $city->getId() ? 'selected = "selected"' : '') : '';
                                                echo '<option value="' . $city->getId() . '" ' . $seleccion . '>' . $city->getName() . '</option>';
                                            }
                                        }
                                        ?>
                                    </select></td>
                                <th>Tel&eacute;fono:</th>
                                <td><input type="text" id="phone" name="phone" value="<?php echo $view->user->getPhone() != "" ? $view->user->getPhone() : ""; ?>"/></td>
                           	</tr>
                            <tr>
                                <th>Calle:</th>
                                <td><input type="text" id="street" name="street" value="<?php echo $view->user->getStreet() != "" ? $view->user->getStreet() : ""; ?>"/></td>
                                <th>Informaci&oacute;n Visible:</th>
			    				<?php $visible = ($view->user->getVisible() == 1)?"checked":"";?>
			    				<td colspan="3"><input type="checkbox" id="visible" name="visible" <?php echo $visible;?> value="1"><label>Esta casilla permite que sus datos 
			    				 se puedan visualizar en el cat&aacute;logo de tiendas</label></td>
                                
                            </tr>
                            <tr>
                                <th>N&uacute;mero:</th>
                                <td><input type="text" id="number" name="number" value="<?php echo $view->user->getNumber() != "" ? $view->user->getNumber() : ""; ?>"/></td>
                                <th>Pago con Creditos:</th>
			    				<?php $crdpay = ($view->user->getCrdPay() == 1)?"checked":"";?>
                                <td colspan="3"><input type="checkbox" id="crd_pay" name="crd_pay" <?php echo $crdpay;?> value="1"><label>Esta casilla autoriza a tus clientes que paguen tus
                                productos con cr&eacute;ditos</label></td>
                          	</tr>
                            <tr>
                                <th>Interior:</th>
                                <td><input type="text" id="numberi" name="numberi" value="<?php echo $view->user->getNumint() != "" ? $view->user->getNumint() : ""; ?>"/></td>
               		    		<?php $delypay = ($view->user->getDelyPay() == 1)?"checked":"";?>
               		    		<th>Pago al entregar:</th>
			    				<td colspan="3"><input type="checkbox" id="dely_pay" name="dely_pay" <?php echo $delypay;?> value="1"><label>Esta casilla autoriza a tus clientes a pagar 
			    				tus productos con cr&eacute;ditos al momento de recibirlos</label></td>
                            </tr>
                            <tr>
                                <th>Colonia:</th>
                                <td><input type="text" id="suburb" name="suburb" value="<?php echo $view->user->getSuburb() != "" ? $view->user->getSuburb() : ""; ?>"/></td>
                                <td></td>
                            </tr>
                            <tr>
                                <th>C&oacute;digo postal:</th>
                                <td><input type="text" id="zip" name="zip" value="<?php echo $view->user->getZip() != "" ? $view->user->getZip() : ""; ?>"/></td>
                                <td></td>
                            </tr>
                            <tr>
			    			</tr>
                        </div>
                    </table>
                    <input type="submit" class="btn btn-default sin-padding pers-btn icono-guardar tam-normal" value=""/>
                </form>
            </div>
        </div>
        <!--</div>-->
    </div>
</div>