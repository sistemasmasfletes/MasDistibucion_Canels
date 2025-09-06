<?php
class Helper_CalendarHelper
{
    /**
     * Regresa el calendario de un usuario
     * @param int $userId 
     * @param bool $displayEventName mostrar o no los nombres de los eventos
     * @return json
     */
    public function getRepository($userId,$displayEventName = true, $color = '')
    {
        /* @var $em Doctrine\ORM\EntityManager */
        $dbs = Model3_Registry::getInstance()->get('databases');
        $em = $dbs['DefaultDb'];
        $user = $em->find('DefaultDb_Entities_User', $userId);
        $meetings = $em->getRepository('DefaultDb_Entities_Meeting')->getAceptedMeetings($user);
        return $this->makeJSON($meetings,$user,$displayEventName,$color);
    }
    
    /**
     * Forma el json para el calendario
     * @param DefaultDb_Entities_Meeting $meetings
     * @param DefaultDb_Entities_User $user
     * @param bool $displayEventName
     * @return json 
     */
    private function makeJSON($meetings,$user,$displayEventName = true ,$color = 'green')
    {
        $credentials = Model3_Auth::getCredentials();
        $rawArray = array();
        $service =array();
        /* @var $meeting DefaultDb_Entities_Meeting*/
        foreach($meetings as $meeting)
        {
            $fecha=$meeting->getMeetingDate();
            /* @var $guest DefaultDb_Entities_User*/
            $guest=$meeting->getGuest();
            
            if ( $displayEventName )
            {
                $id = $meeting->getId();
                $class=($guest->getId()== $credentials['id'])? 'red':'green';
            }
            else
            {
                $id = 0;
                $class = $color;
            }
                        
            switch($guest->getTypeUser()){
                case 1: $typeUser='Buyer';
                    break;
                case 2: $typeUser='Seller';
                    break;
                case 3: $typeUser= 'Both';
                    break;
                case 4: $typeUser= 'Neither';
                    break;
            }   
      
            /**
             * si nosotros somos los invitados, cambiamos los datos pues saldremos en nuestro propio calendario
             */
            if( $guest->getId()!= $credentials['id'] )
            {
                $title = utf8_encode( $guest->getFirstName().' '.$guest->getCompany() );
                $ownerName = utf8_encode($meeting->getOwner()->getFirstName());
                $guestName = utf8_encode($guest->getFirstName());
                $company = utf8_encode($guest->getCompany());
            }
            else
            {
                $title = utf8_encode($meeting->getOwner()->getFirstName() .' '.$meeting->getOwner()->getCompany() );
                $ownerName = utf8_encode($guest->getFirstName());
                $guestName = utf8_encode($meeting->getOwner()->getFirstName());
                $company = utf8_encode($meeting->getOwner()->getCompany());
            }
            
            $rawArray[] = array(
                'title' => utf8_encode($displayEventName == true ?  $title : 'Busy'),
                'start' => $fecha->format('Y-m-d H:i:s'),
                'end' => $fecha->add(new DateInterval('PT'.$user->getMeetingDuration().'M'))->format('Y-m-d H:i:s'),
                'className' => $class,
                'description' => utf8_encode($meeting->getMessage()),
                'owner' => $ownerName,
                'guest' => $guestName,
                'company'=> $company,
                'interests'=> $typeUser,
                'website'=>$guest->getCompanyUrl(),
                'id' => $id,
                'allDay'=>false);
        }
        
        //Revisamos si el usuario tiene activado el descanso e insertamos eventos en 
        if($user->getUseBreak())
        {
            /**
             * @todo Ésto debe ser obtenido de la configuracion del evento 
             */
            $eventStart = mktime(0,0,0,"03","29","2011");
            $eventEnd = mktime(0,0,0,"04","1","2011");
            $leap = 24 * 60 * 60;
            
            for( $i = $eventStart; $i <= $eventEnd; $i += $leap )
            {
                $rawArray[] = array(
                'title' => 'Break',
                'start' => date('Y-m-d ',$i).$user->getBreakStart()->Format('H:i:s'),
                'end' => date('Y-m-d ',$i).$user->getBreakEnd()->Format('H:i:s'),
                'className' => 'blue',
                'allDay'=>false,
                'selectable'=>false,
                'id'=>0);
            }
        }
        
        return json_encode($rawArray);
    }
}
?>
