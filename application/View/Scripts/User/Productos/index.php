<!-- Modal para mostrar QR -->
<div id="qrModal" class="qr-modal">
    <div class="qr-modal-content">
        <h3>Código QR del Pasajero</h3>
        <div id="qrCode"></div>
        <p id="qrInfo"></p>
        
        <div class="whatsapp-section">
            <h4>Enviar por WhatsApp</h4>
            <p>A continuación, se abrirá WhatsApp. Tu código QR se descargará solo; por favor, adjunta ese archivo de imagen.</p>
            <input type="tel" 
                   id="whatsappNumber" 
                   class="phone-input" 
                   placeholder="Ingresa el número con código de país. Ej: 521234567890"
                   pattern="[0-9]{10,15}">
            <div>
                <button id="sendWhatsApp" class="whatsapp-btn">
                    <i class="bi bi-whatsapp"></i> Enviar por WhatsApp
                </button>
            </div>
        </div>
        
        <div>
            <button id="downloadQR" class="download-btn">
                <i class="bi bi-download"></i> Descargar QR
            </button>
            <button onclick="closeQRModal()" class="close-btn">
                <i class="bi bi-x"></i> Cerrar
            </button>
        </div>
    </div>
</div>


<!-- Tabla -->
<div class="contiene-bread">
    <ol class="breadcrumb">
        <li class="active">Panel</li>
        <li class="active "> <a href="/public/User/Catalogos">Catálogos</a></li>
        <li class="active actualpg ">Productos</li>
    </ol>
</div>
<div class="container">
    <div class="row" >
        <!--<div class="span12 blockGray">-->
            <div class="blockInner">
                <h1>Pasajeros del catalogo: <?php echo $view->catalog->getTitle();?> </h1>
                <?php
                echo '<a class="addbtn" href="' . $view->url(array('action' => 'add'),true) . '" ><i class="bi bi-person-add"></i>Nuevo pasajero</a>';
                echo '<div class="clear" style="height:15px;" ></div>';
                if($view->products != null)
                {
                    if(count($view->products) == 1 && $view->products[0]->getStatus() == 2)
                    { ?>
                        <div class="alert alert-info">
                        <strong>Actualmente no tiene productos asociados a este cat&aacute;logo</strong>, agr&eacute;gelos dando clic en el bot&oacute;n "Agregar Producto" ubicado en la parte superior.
                        </div> 
                        <?php
                    } 
                    else
                    {
                ?>
                <table class="table table-striped table-bordered table-condensed">
                    <thead>
                        <tr>
                            <th>N° Nomina</th>
                            <th>Nombre</th>
                            <th>Apellidos</th>
                            <th>Clasificacion1</th>
                            <th>Clasificacion2</th>
                            <th>Prioridad</th>
                            <!-- <th>Precio</th>
                            <th>Precio Normal</th>
                            <th>Stock</th>
                            <th>Destacado</th>
                            <th>SKU</th> -->
                            <th>QR</th>
                            <th>Editar</th>
                            <th>Eliminar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            foreach($view->products as $p)
                            {
                                if($p->getStatus() == 1):
                                    $idPayroll = $p->getIdPayroll();
                                    $hasPayroll = !empty($idPayroll) && trim($idPayroll) !== '';
                                    ?>
                                 <tr>
                                    <td><?php echo $idPayroll;?></td>
                                    <td><?php echo $p->getName();?></td>
                                    <td><?php echo $p->getLastName();?></td>
                                    <td><?php echo $p->getClasificacion1();?></td>
                                    <td><?php echo $p->getClasificacion2();?></td>
                                    <td>
                                        <?php
                                        $priorityValue = $p->getPriority();

                                        if ($priorityValue == 0) {
                                            echo "Sin prioridad";
                                        } elseif ($priorityValue == 1) {
                                            echo "Prioridad media";
                                        } elseif ($priorityValue == 2) {
                                            echo "Máxima prioridad";
                                        } else {
                                            echo "No definido";
                                        }
                                        ?>
                                    </td>                                    
                                    <td>
                                        <?php if ($hasPayroll): ?>
                                            <button onclick="generateQR('<?php echo $idPayroll;?>', '<?php echo $p->getName() . ' ' . $p->getLastName();?>')" class="qr-btn">
                                                <i class="bi bi-qr-code"></i> QR
                                            </button>                              
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?php echo $view->url(array('module'=>'User','controller'=>'Productos','action'=>'edit','id'=>$view->catalog->getId(),'idProduct'=>$p->getId()));?>" class="edit-link">
                                            Editar
                                        </a>
                                    </td>
                                    <td>
                                        <a href="<?php echo $view->url(array('module'=>'User','controller'=>'Productos','action'=>'delete','id'=>$view->catalog->getId(),'idProduct'=>$p->getId()));?>" class="delete-link">
                                            Eliminar
                                        </a>
                                    </td>
                                </tr>
                                <?php
                                        endif;
                            }
                        ?>
                    </tbody>
                </table>
                <?php
                    }
                }
                else
                {
                    ?>
                    <div class="alert alert-info">
                        <strong>Actualmente no tiene productos asociados a este cat&aacute;logo</strong>, agr&eacute;gelos dando clic en el bot&oacute;n "Agregar Producto" ubicado en la parte superior.
                    </div>
                    <?php
               }
                ?>
            </div>
        <!--</div>-->
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<script>
var currentQR = null;
var currentIdPayroll = null;
var currentPassengerName = null;

function generateQR(idPayroll, passengerName) {
    currentIdPayroll = idPayroll;
    currentPassengerName = passengerName;
    
    document.getElementById('qrModal').style.display = 'flex';
    document.getElementById('qrCode').innerHTML = '';
    document.getElementById('whatsappNumber').value = '';
    document.getElementById('qrInfo').textContent = 'ID: ' + idPayroll + ' - ' + passengerName;
    
    currentQR = new QRCode(document.getElementById('qrCode'), {
        text: idPayroll.toString(),
        width: 200,
        height: 200,
        colorDark: "#000000",
        colorLight: "#ffffff",
        correctLevel: QRCode.CorrectLevel.H
    });
}

function closeQRModal() {
    document.getElementById('qrModal').style.display = 'none';
    if (currentQR) {
        currentQR.clear();
    }
}

function validatePhoneNumber(phone) {
    var cleaned = phone.replace(/\D/g, '');
    if (cleaned.length < 10 || cleaned.length > 15) {
        return false;
    }
    return cleaned;
}

function sendToWhatsApp(phoneNumber) {
    if (!currentQR) return;
    var canvas = document.querySelector('#qrCode canvas');
    if (!canvas) return;
    var qrImage = canvas.toDataURL('image/png');
    var message = "Código QR del pasajero:\n";
    message += "ID: " + currentIdPayroll + "\n";
    message += "Nombre: " + currentPassengerName + "\n";
    message += "Escanea este código QR para obtener la información.";
    var link = document.createElement('a');
    link.href = qrImage;
    link.download = 'QR_' + currentIdPayroll + '.png';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    var whatsappUrl = 'https://wa.me/' + phoneNumber + '?text=' + encodeURIComponent(message);
    window.open(whatsappUrl, '_blank');
    alert('Se ha abierto WhatsApp. Por favor, adjunta la imagen del QR que se descargó automáticamente.');
}

function downloadQR() {
    if (!currentQR) return;
    var canvas = document.querySelector('#qrCode canvas');
    if (!canvas) return;
    var image = canvas.toDataURL('image/png');
    var downloadLink = document.createElement('a');
    downloadLink.href = image;
    downloadLink.download = 'QR_' + currentIdPayroll + '.png';
    
    document.body.appendChild(downloadLink);
    downloadLink.click();
    document.body.removeChild(downloadLink);
}

document.getElementById('downloadQR').addEventListener('click', downloadQR);
document.getElementById('sendWhatsApp').addEventListener('click', function() {
    var phoneInput = document.getElementById('whatsappNumber').value;
    if (!phoneInput) {
        alert('Por favor ingresa un número de WhatsApp');
        return;
    }
    var cleanedNumber = validatePhoneNumber(phoneInput);
    if (!cleanedNumber) {
        alert('Por favor ingresa un número válido. Ejemplo: 521234567890 (código de país + número)');
        return;
    }
    sendToWhatsApp(cleanedNumber);
});

document.getElementById('whatsappNumber').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        document.getElementById('sendWhatsApp').click();
    }
});

document.getElementById('qrModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeQRModal();
    }
});

document.getElementById('qrModal').addEventListener('shown', function() {
    document.getElementById('whatsappNumber').focus();
});
</script>