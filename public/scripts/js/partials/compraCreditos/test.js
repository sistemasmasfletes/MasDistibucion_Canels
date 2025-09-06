function uploadMultipleFiles( files ){
	var limit = 1048576*2,//2MB
	xhr,
	mensaje = select('p#mensaje');

	if( files[0] != undefined ){

		if( !confirm('Cargar '+files.length+' archivos?') )
			return false;

		mensaje.innerHTML = 'Empezando la carga...';

		for(var i=0;i<files.length;i++){
			var current_file = files[i];

			mensaje.innerHTML = 'Cargando archivo '+(i+1)+'...';

			if( current_file.size < limit ){
				xhr = new XMLHttpRequest();

				xhr.upload.addEventListener('error',function(e){
					alert('Ha habido un error cargando el archivo '+(i+1));
					return false;
				}, false);

				xhr.open('POST','upload.php');

	            xhr.setRequestHeader("Cache-Control", "no-cache");
	            xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
	            xhr.setRequestHeader("X-File-Name", current_file.name);

	            xhr.send(current_file);
			}else{
				alert('El archivo '+(i+1)+' es mayor de 2MB!');
				mensaje.innerHTML = 'El archivo '+(i+1)+' es mayor de 2MB!';
				return false;
			}
		}

		mensaje.innerHTML = 'Carga completa!';
	}
}

function select( str ){
	return document.querySelector(str);
}

var upload_button = select('#subir');

upload_button.onclick = function(e){
	e.preventDefault();
	this.disabled = 'true';

	var archivo1 = select('#path').files[0],
	//archivo2 = select('#archivo2').files[0],
	//archivo3 = select('#archivo3').files[0],
	//todos_los_archivos = [archivo1,archivo2,archivo3];
        todos_los_archivos = [archivo1];

	uploadMultipleFiles(todos_los_archivos);
}