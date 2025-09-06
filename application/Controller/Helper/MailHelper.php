<?php

/**
 * Description of Helper_Mail
 * Helper mail es un helper de envio de correo electronico, si requieres una correo en especial
 * configura un metodo.
 *
 * @author us
 */
class Helper_MailHelper
{
    /* Cambiar el banner cuando se tenga uno mejor y su ruta, y la direccion de correo de la firma */

    private $_banner = '<div style="padding: 14px; margin-bottom: 4px; background-color: rgb(0, 0, 0); -moz-border-radius: 5px 5px 5px 5px;">
                            <a target="_blank" href="http://www.masfletes.com" style="color: rgb(0, 0, 0);">
                                <img width="186" height="83" style="display: block; border: 0pt none;"
                                    src="http://logistic-apps.com/masfletes/images/logo-masfletes.gif" alt="MasFletes">
                            </a>
                           </div>';
    private $_title_start = '<h2 style="margin: 0pt 0pt 16px; font-size: 18px; font-weight: normal;">';
    private $_title_end = '</h2>';
    private $_title = '';
    private $_body_start = '<div style="background-color:white;"><p style="  color: #000000; font-family: Helvetica,Arial,sans-serif; font-size: 12px;">';
    private $_body_end = '</p></div>';
    private $_body_html = '';
    private $_body_alt = '';
    private $_firma_HTML = '<span style="font: 13px Georgia; color: rgb(102, 102, 102);">Mas Fletes</span><hr>
                            <div style="text-align: center; color: rgb(102, 102, 102);">
                            Please do not reply to this email; replies are not monitored.<br/>
                            Logistic Apps<br/>
                            S.L.P. México<br/>
                            </div>';
    private $_firma_ALT = 'Logistic Apps | S.L.P. Mexico';
    private $_mail = null;

    function __construct()
    {
        if ($this->_mail == null)
            $this->_mail = new Mailer_Sender();  // solo cuando es null la instanciamos
    }

    /**
     * Metodo privado que crea, y prepara las situaciones para envio de mail.
     * @param string $destiny
     * @return mixed false: error al crear mail sender , Mailer_Sender
     */
    private function preparedSend($fromName = 'MasFletes')
    {
        $mail = $this->_mail;
        $mail->IsSMTP();
        $mail->Mailer = "smtp";
        $mail->Host = 'ssl://email-smtp.us-east-1.amazonaws.com';
        $mail->Port = 465;
        $mail->SMTPAuth = true;        
        $mail->Username = 'AKIAIU6WHO4LZ4UVK5GQ';
        $mail->Password = 'AskCSFszKBX3B+xA75olDrYt9TnCTUlPa269AkiDuMos';
        $mail->From = 'noreply@dealbusinesscenter.com';
        $mail->FromName = $fromName;
        
//        $mail->SMTPDebug = 1;
//        $mail->Mailer = "smtp";
//        $mail->Host = 'smtp.gmail.com';
//        $mail->Port = 587;
//        $mail->SMTPAuth = true;        
//        $mail->Username = 'usielc@gmail.com';
//        $mail->Password = '';
//        $mail->From = 'usielc@gmail.com';
//        $mail->FromName = $fromName;
//        if($mail->Host=='smtp.gmail.com' || $mail->Host=='smtp.live.com')
//        {
//            $mail->SMTPSecure = 'tls';
//        }
        return $mail;
    }
    
    private function setMail($mail, $subject)
    {
        $msjHTML = '';
        $msjHTML .= $this->_title_start . $this->_title . $this->_title_end . '<br><br>';
        $msjHTML .= $this->_body_start . $this->_body_html . $this->_body_end . '<br>';
        $msjHTML .= $this->_firma_HTML;
        $subject = utf8_decode($subject);
        $mail->Subject = $subject;
        $msjAlt = $this->_body_alt . $this->_firma_ALT;

        $mail->AltBody = $msjAlt;
        $mail->Body = $msjHTML;
        $pattern = array(
            '/Ã¡/' => '&aacute;',
            '/Ã©/' => '&eacute;',
            '/Ã­/' => '&iacute;',
            '/Ã³/' => '&oacute;',
            '/Ãº/' => '&uacute;',
            '/Ã/' => '&Aacute;',
            '/Ã/' => '&Eacute;',
            '/Ã/' => '&Iacute;',
            '/Ã/' => '&Oacute;',
            '/Ã/' => '&Uacute;',
            '/\n/' => '<br>'
        );
        $mail->Body = preg_replace(array_keys($pattern), array_values($pattern), $mail->Body);
        $mail->IsHTML(true);
        $this->_title = '';
        $this->_body_alt = '';
        $this->_body_html = '';
        return $mail;
    }
    
    /**
     * Metodo que envia el correo electronico  n numero de veces,
     * @param Mailer_Sender $mail
     * @param int $numTrials
     * @return boolean
     */
    private function sendMail($mail, $numTrials=4)
    {
        $exito = $mail->Send();
        $intentos = 1;
        while ((!$exito) && ($intentos < $numTrials))
        {
            sleep(5);
            $exito = $mail->Send();
            $intentos = $intentos + 1;
        }
        $mail->ClearAddresses();
        return $exito;
    }

    /**
     * Metodo que envia un correo al enviar una invitacion
     * @param <type> $destiny
     * @param <type> $message
     * @param <type> $numTrials
     * @return <type>
     */
    public function sendMailToSecretary($destiny, $clients, $numTrials=4)
    {
        $mail = $this->preparedSend();
        $mail->AddAddress($destiny->getUsername());
        $subject = 'Envio de facturas';
        $this->_title = 'Envio de facturas <br/>';
        $this->_body_html .= 'Sr(ita).<strong>'.$destiny->getFirstName().' '.$destiny->getLastName().'</strong>,<br/>
        Favor de enviar facturas a los siguientes usuarios.<br/><br/>';
        $this->_body_html .= '<strong>Usuario: Correo-Electronico </strong><br/>';
        foreach($clients as $client){
            $this->_body_html .= $client->getFirstName() . ' ' . $client->getLastName() . ': '. $client->getUsername() . '<br/>';
        }
//        $this->_body_html .= '<br/>';
        $mail = $this->setMail($mail, $subject);
        $success = $this->sendMail($mail, $numTrials);
        return $success;
    }
}
