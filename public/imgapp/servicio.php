<?php  
                //$this->view->setUseTemplate(FALSE);
                $idop = $_POST['id_op'];
        
                $dir ='/public/imageapp/imagesop/operation_'.$idop.'/';
                if(!file_exists($dir)) {
                        mkdir($dir, 0777, true);
                        $tfiles = 0;
                }else{
                                //$tfiles = count(glob($dir.'{*.jpg,*.gif,*.png}',GLOB_BRACE));
                                $explorar = scandir($dir);
                                /*$idx = 0;
                                foreach ($explorar as $file){
                                        if($idx > 2){
                                                $fil = explode('.', $file);
                                                var_dump( $fil[1]);
                                        }
                                        $idx++; 
                                }*/
                                
                                $tfiles = count($explorar) - 3;
                }
                $tfiles = $tfiles + 1;
                $pic = $_FILES['files'];
                $exten = explode('.', $pic['name']);
                $data = array('success' => true);
                //Validamos si la copio correctamente
                //if(copy($pic['tmp_name'],$dir.$idop."_".$pic['name'])){
                if(copy($pic['tmp_name'],$dir.$idop."_".$tfiles.'.'.strtolower($exten[1]))){
                        $data = array('success' => true, 'name' => $idop."_".$tfiles.'.'.strtolower($exten[1]));
                }
        
                $filename = $dir.$idop.'.zip';
                $zip = new ZipArchive();
                if($zip->open($filename,ZIPARCHIVE::CREATE)===true) {
                        //$zip->addFile($dir.$idop."_".$pic['name']);
                        $zip->addFile($dir.$idop."_".$tfiles.'.'.$exten[1]);
                        $zip->close();
                }

?>              