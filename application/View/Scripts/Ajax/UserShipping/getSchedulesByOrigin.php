<?php use Doctrine\ORM\Mapping\Driver\DatabaseDriver;
/* @var $view Model3_View */?> 
<?php 
echo '<option value="0">Seleccione...</option>';

$array_dias['Sunday'] = "Domingo";
$array_dias['Monday'] = "Lunes";
$array_dias['Tuesday'] = "Martes";
$array_dias['Wednesday'] = "Miercoles";
$array_dias['Thursday'] = "Jueves";
$array_dias['Friday'] = "Viernes";
$array_dias['Saturday'] = "Sabado";

foreach ($view->schedules as $schedule) 
{
	switch ((int)$schedule['route_id']){
		case 1:
				$bc="C0EECE";
				$ftc="013506";
			break;
		case 12:
				$bc="BED9F9";
				$ftc="0C023C";
			break;
		case 13:
				$bc="FDA8A4";
				$ftc="400401";
			break;
				
			default:
				$bc="001146";
				$ftc="FFF";
	}
	
	$day = $array_dias[date('l', strtotime($schedule['start_date']))];
	
    echo '<option style="color: #'.$ftc.'; background-color: #'.$bc.';" value="' . $schedule['id'] . '">' . $schedule['start_date'] . ' ' . $schedule['name'] . ' ' . $day. '</option>';
}