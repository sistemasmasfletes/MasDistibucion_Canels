<?php

/**
 * Description of ArchiveHelper
 *
 * @author usuario
 */
class Helper_ArchiveHelper
{
    /**
     * Tamaño maximo de archivo
     */
    const FILE_SIZE_MAX = 5242880;
    
    /**
     * Constantes para validar el tipo de archivo que pedimos
     */
    const TYPE_ALL = 1;
    const TYPE_IMAGE = 2;
    const TYPE_DOCUMENT = 3;

    public function loadArchive($archive, $idUsuario, $type = self::TYPE_ALL)
    {
        $idUsuario = '';
        $res = array('result' => false, 'msg' => '', 'path' => '');
        if (!empty($archive))
        {

            $archivo = $archive["tmp_name"];
            $tamanio = $archive["size"];
            $tipo = $archive["type"];
            $nombre = $archive["name"];
            $subTipo = explode('/', $tipo);
            $ext = explode('.', $archive["name"]);
            $ext = $ext[count($ext)-1];
            
            if ($tamanio > self::FILE_SIZE_MAX)
            {
                $res['result'] = false;
                $res['msg'] = 'File is too big!! Max 5.0 MB';
            }
            else
            {
                if (($type == self::TYPE_ALL || $type == self::TYPE_IMAGE) && $subTipo[0] === 'image')
                {
                    $dirUpload = 'data/images/usr' . $idUsuario . '/';
                    $pathImage = $dirUpload . 'img' . $idUsuario . '_' . date('YmdHis') . '.' . $ext;
                    $res['path'] = $pathImage;
                    $dirUpload = 'data/images/usr' . $idUsuario . '/';
                    $fs = new Model3_FileSystem();
                    if (!$fs->existeDir($dirUpload))
                    {
                        $fs->creaDir($dirUpload);
                    }
                    
                    $res['result'] = true;
                    if (!move_uploaded_file($archivo, $pathImage))
                    {
                        $res['result'] = false;
                        $res['msg'] = "Ocurrio un error al procesar la imagen...";
                    }
                }
                else if (($type == self::TYPE_ALL || $type == self::TYPE_DOCUMENT) && ($subTipo[0] === 'text' || $subTipo[1] === 'pdf' || $subTipo[1] === 'vnd.openxmlformats-officedocument.wordprocessingml.document'))
                {
                    $dirUpload = 'data/documents/usr' . $idUsuario . '/';
                    $pathImage = $dirUpload . 'doc' . $idUsuario . '_' . date('YmdHis') . '.' . $ext;
                    $res['path'] = $pathImage;
                    $fs = new Model3_FileSystem();
                    if (!$fs->existeDir($dirUpload))
                    {
                        $fs->creaDir($dirUpload);
                    }
                    
                    $res['result'] = true;
                    if (!move_uploaded_file($archivo, $pathImage))
                    {
                        $res['result'] = false;
                        $res['msg'] = "Ocurrio un error al procesar la imagen...";
                    }
                }
                else
                {
                    $res['result'] = false;
                    $res['msg'] = "Formato incorrecto.";
                }
            }
        }
        else
        {
            $res['result'] = false;
            $res['msg'] = "No se puede obtener la imagen";
        }
        return $res;
    }

    public function loadHeadShot($archive, $idUsuario, $type = self::TYPE_ALL)
    {
        $idUsuario = '';
        $res = array('result' => false, 'msg' => '', 'path' => '');
        //Si hay una imagen a subir 
        if (!empty($archive))
        {

            $archivo = $archive["tmp_name"];
            $tamanio = $archive["size"];
            $tipo = $archive["type"];
            $nombre = $archive["name"];
            $subTipo = explode('/', $tipo);
            $ext = explode('.', $archive["name"]);
            $ext = $ext[count($ext)-1];
            
            // si el tamaño es mayor a 5.0 MB manda un mensaje de error
            if ($tamanio > self::FILE_SIZE_MAX)
            {
                $res['result'] = false;
                $res['msg'] = 'File is too big!! Max 5.0 MB';
            }
            else
            {
                //Solo dejara subir archivos de tipo imagen 
                if (($type == self::TYPE_ALL || $type == self::TYPE_IMAGE) && $subTipo[0] === 'image')
                {
                    $dirUpload = 'data/images/usr' . $idUsuario . '/';
                    $pathImage = $dirUpload . 'img' . $idUsuario . '_' . date('YmdHis') . '.' . $ext;
                    $res['path'] = $pathImage;
                    $dirUpload = 'data/images/usr' . $idUsuario . '/';
                    $fs = new Model3_FileSystem();
                    if (!$fs->existeDir($dirUpload))
                    {
                        $fs->creaDir($dirUpload);
                    }
                    
                    $res['result'] = true;
                    if (!move_uploaded_file($archivo, $pathImage))
                    {
                        $res['result'] = false;
                        $res['msg'] = "Ocurrio un error al procesar la imagen...";
                    }
                }
                else
                {
                    $res['result'] = false;
                    $res['msg'] = "Formato incorrecto.";
                }
            }
        }
        else
        {
            $res['result'] = false;
            $res['msg'] = "No se puede obtener la imagen";
        }
        return $res;
    }
        
    public function clearArchive($archive)
    {
        @unlink($archive);
    }

}
