<div id="Contenedor">
    <div style="width: 706px; background-color: #e5e5e5; border-radius: 15px; margin-top: 35px;">
        <img style="margin: 10px;" src="<?php echo $view->getBaseUrl() . '/images/icono-enviar.jpg' ?>" alt="enviar"/>
        <p class="TextoPortadaGral">Podr&aacute;s <strong>PUBLICAR la Ruta</strong> que seguir&aacute; tu Unidad de Carga vac&iacute;a despu&eacute;s de hacer su Flete original. <br/>
            Tambi&eacute;n podr&aacute;s <strong>VER las Cargas Disponibles</strong> publicadas por el enviador. </p>
    </div>
    <div style="background-color: #393939" class="bienvenido">
        <span class="TituloBienvenido">Bienvenido Transportista</span><br/>
            <span class="TextoNombreUsuario"><br/>
                &nbsp; &nbsp; &nbsp; &nbsp;  nombre de usuario</span><br>
            <img src="<?php echo $view->getBaseUrl() . '/images/boton-cerrar-sesion.gif' ?>" height="27" vspace="8" width="108"/>
    </div>
    <div class="transport">
        <table align="center" bgcolor="#FFFFFF" border="0" cellpadding="3" cellspacing="0" width="95%">
            <tbody><tr>
                    <td colspan="6" class="TituloTablaPrincipal" bgcolor="#E5E5E5" height="35">Publicar Nueva RUTA</td>
                </tr>
                <tr>
                    <td class="TituloTabla" align="center" bgcolor="#666666" height="30">No.</td>
                    <td class="TituloTabla" bgcolor="#666666">Origen</td>
                    <td class="TituloTabla" bgcolor="#666666">Destino</td>
                    <td class="TituloTabla" bgcolor="#666666">Fecha</td>
                    <td class="TituloTabla" bgcolor="#666666">Tipo de Veh&iacute;culo </td>
                    <td class="TituloTabla" bgcolor="#666666">Tipo de Carga </td>
                </tr>
                <?php for ($i = 0; $i < 5; $i++): ?>
                    <tr>
                        <td class="TextoTabla" align="center">30</td>
                        <td class="TextoTabla">San Luis Potos&iacute; / S.L.P.</td>
                        <td class="TextoTabla">Abasolo / GTO. </td>
                        <td class="TextoTabla">20-Abril-2011 </td>
                        <td class="TextoTabla">Torthon</td>
                        <td class="TextoTabla">Maquinaria</td>
                    </tr>
                <?php endfor; ?>
            </tbody></table> 
    </div>
    <div class="rutanueva"><br/>
        <span class="titulosOrdenesSelect">
           Publicar Nueva RUTA
        </span><br/><br/>
         <table>
          <tbody>
          <tr>
            <td class="TextoPortadaGral" align="right" bgcolor="#FFFFFF" height="30" width="156">Origen:&nbsp;</td>
            <td bgcolor="#FFFFFF" width="217"><select name="select" class="InputText" id="select">
                    <option>San Luis Potos&iacute;</option>
            </select></td>
          </tr>
          <tr>
            <td class="TextoPortadaGral" align="right" bgcolor="#FFFFFF" height="30">Destino:&nbsp;</td>
            <td bgcolor="#FFFFFF"><select name="select2" class="InputText" id="select2">
                    <option>San Luis Potos&iacute;</option>
            </select></td>
          </tr>
          <tr>
            <td class="TextoPortadaGral" align="right" bgcolor="#FFFFFF" height="30">Fecha:&nbsp;</td>
            <td bgcolor="#FFFFFF"><input value="" name="textfield" class="InputText" id="textfield" type="text"></td>
          </tr>
          <tr>
              <td class="TextoPortadaGral" align="right" bgcolor="#FFFFFF" height="30">Tipo de Veh&iacute;culo:&nbsp;</td>
            <td bgcolor="#FFFFFF"><select name="select4" class="InputText" id="select4">
              <option>Camioneta de 3.5</option>
            </select></td>
          </tr>
          <tr>
            <td class="TextoPortadaGral" align="right" bgcolor="#FFFFFF" height="35">&nbsp;</td>
            <td bgcolor="#FFFFFF"><img name="imageField" id="imageField" src="<?php echo $view->getBaseUrl() . '/images/boton-publicar.gif' ?>" /></td>
          </tr>
         </tbody></table>
    </div>
    <div class="transport">
        <table align="center" bgcolor="#FFFFFF" border="0" cellpadding="3" cellspacing="0" width="95%">
            <tbody><tr>
                    <td colspan="6" class="TituloTablaPrincipal" bgcolor="#E5E5E5" height="35">Ver CARGAS Disponibles</td>
                </tr>
                <tr>
                    <td class="TituloTabla" align="center" bgcolor="#666666" height="30">No.</td>
                    <td class="TituloTabla" bgcolor="#666666">Origen</td>
                    <td class="TituloTabla" bgcolor="#666666">Destino</td>
                    <td class="TituloTabla" bgcolor="#666666">Fecha</td>
                    <td class="TituloTabla" bgcolor="#666666">Tipo de Veh&iacute;culo </td>
                    <td class="TituloTabla" bgcolor="#666666">Tipo de Carga </td>
                </tr>
                <?php for ($i = 0; $i < 5; $i++): ?>
                    <tr>
                        <td class="TextoTabla" align="center">30</td>
                        <td class="TextoTabla">San Luis Potos&iacute; / S.L.P.</td>
                        <td class="TextoTabla">Abasolo / GTO. </td>
                        <td class="TextoTabla">20-Abril-2011 </td>
                        <td class="TextoTabla">Torthon</td>
                        <td class="TextoTabla">Maquinaria</td>
                    </tr>
                <?php endfor; ?>
            </tbody></table> 
    </div>
    <div class="rutanueva">
        <br/>
        <span class="titulosOrdenesSelect">
            Buscar CARGAS Disponibles
        </span>
        <br/><br/>
         <table>
          <tbody>
          <tr>
            <td class="TextoPortadaGral" align="right" bgcolor="#FFFFFF" height="30" width="156">Origen:&nbsp;</td>
            <td bgcolor="#FFFFFF" width="217"><select name="select" class="InputText" id="select">
                    <option>San Luis Potos&iacute;</option>
            </select></td>
          </tr>
          <tr>
            <td class="TextoPortadaGral" align="right" bgcolor="#FFFFFF" height="30">Destino:&nbsp;</td>
            <td bgcolor="#FFFFFF"><select name="select2" class="InputText" id="select2">
                    <option>San Luis Potos&iacute;</option>
            </select></td>
          </tr>
          <tr>
            <td class="TextoPortadaGral" align="right" bgcolor="#FFFFFF" height="30">Fecha:&nbsp;</td>
            <td bgcolor="#FFFFFF"><input value="" name="textfield" class="InputText" id="textfield" type="text"></td>
          </tr>
          <tr>
              <td class="TextoPortadaGral" align="right" bgcolor="#FFFFFF" height="30">Tipo de Veh&iacute;culo:&nbsp;</td>
            <td bgcolor="#FFFFFF"><select name="select4" class="InputText" id="select4">
              <option>Camioneta de 3.5</option>
            </select></td>
          </tr>
          <tr>
            <td class="TextoPortadaGral" align="right" bgcolor="#FFFFFF" height="35">&nbsp;</td>
            <td bgcolor="#FFFFFF"><img name="imageField" id="imageField" src="<?php echo $view->getBaseUrl() . '/images/boton-buscar.gif' ?>" /></td>
          </tr>
         </tbody></table>
    </div>
</div>
