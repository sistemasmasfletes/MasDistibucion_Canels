<?php
/**
 * Description of Categories
 *
 * @author logistic
 */
class View_Helper_ManagerImages extends Model3_View_Helper
{
    public function img($path, $alt="", $title="", $attrs='', $infoImg = true)
    {
        if($infoImg)
        {
            $infoImg = @getimagesize($path);
        }
        $parts = explode('/', $path);
        if(empty($alt) === true)
        {
            $alt = $path; //$parts[-1];
        }
        if(empty($title) === true)
        {
            $title = $path; //$parts[-1];
        }
        $code = '<img src="'.$this->_view->getBaseUrlPublic().'/'.$path.'" alt="'.$alt.'" title="'.$title.'"'.($infoImg ? '" width="'.$infoImg[0].'px" height="'.$infoImg[1].'px"' : '').$attrs.'/>';
        return $code;
    }

    public function imgFullHost($fullHost, $path, $alt="", $title="", $attrs='', $infoImg = true)
    {
        if($infoImg)
        {
            $infoImg = @getimagesize($path);
        }
        $parts = explode('/', $path);
        if(empty($alt) === true)
        {
            $alt = $path; //$parts[-1];
        }
        if(empty($title) === true)
        {
            $title = $path; //$parts[-1];
        }
        $code = '<img src="'.$fullHost.$path.'" alt="'.$alt.'" title="'.$title.'"'.($infoImg ? '" width="'.$infoImg[0].'px" height="'.$infoImg[1].'px"' : '').$attrs.'/>';
        return $code;
    }

    /**  Metodo creado para hacer Thumbs de imagenes, se debe especificar
     *
     */
    public function thumbImg($path, $alt="", $title="", $attrs='', $maxWidth=null, $maxHeight=null, $filtro=null)
    {
        $foto = $path;

        if(empty($alt) === true)
        {
            $alt = $path; //$parts[-1];
        }
        if(empty($title) === true)
        {
            $title = $path; //$parts[-1];
        }

        /* para la libreria */
        if(is_null($maxWidth))
        {
            $w = '&w=84';
            $width = '84';
        }
        else
        {
            $w = '&w='.$maxWidth;
            $width = $maxWidth;
        }

        if(is_null($maxHeight))
        {
            $h = '&h=50';
            $heigt = '50';
        }
        else
        {
            $h = '&h='.$maxHeight;
            $heigt = $maxHeight;
        }

        if(is_null($filtro))
        {
            $fltr = '';
        }
        else
        {
            $fltr = '&fltr[]=ric|'.$filtro.'|'.$filtro;
        }

        if($path != '')
        {
            $foto = 'lib/phpThumb/phpThumb.php?src='.$this->_view->getBaseUrlPublic().'/'.$path.$w.$h.'&zc=1'.$fltr.'&far=1&f=png';
        }

        $code = '<img src="'.$this->_view->getBaseUrlPublic().'/'.$foto.'" alt="'.$alt.'" title="'.$title.'" width="'.$width.'px" height="'.$heigt.'px" '.$attrs.'/>';
        return $code;
    }

    /**  Metodo creado para hacer Thumbs de imagenes, se debe especificar
     *
     */
    public function thumbImgFullHost($fullHost,$path, $alt="", $title="", $attrs='', $maxWidth=null, $maxHeight=null, $filtro=null)
    {
        $foto = $path;

        if(empty($alt) === true)
        {
            $alt = $path; //$parts[-1];
        }
        if(empty($title) === true)
        {
            $title = $path; //$parts[-1];
        }

        /* para la libreria */
        if(is_null($maxWidth))
        {
            $w = '&w=84';
            $width = '84';
        }
        else
        {
            $w = '&w='.$maxWidth;
            $width = $maxWidth;
        }

        if(is_null($maxHeight))
        {
            $h = '&h=50';
            $heigt = '50';
        }
        else
        {
            $h = '&h='.$maxHeight;
            $heigt = $maxHeight;
        }

        if(is_null($filtro))
        {
            $fltr = '';
        }
        else
        {
            $fltr = '&fltr[]=ric|'.$filtro.'|'.$filtro;
        }

        if($path != '')
        {
            $foto = 'lib/phpThumb/phpThumb.php?src='.$fullHost.$path.$w.$h.'&zc=1'.$fltr.'&far=1&f=png';
        }

        //$code = '<img src="'.$this->_view->getBaseUrlPublic().'/'.$foto.'" alt="'.$alt.'" title="'.$title.'" width="'.$width.'px" height="'.$heigt.'px" '.$attrs.'/>';

        $code = '<img src="'.$fullHost.$path.'" alt="'.$alt.'" title="'.$title.'" width="'.$width.'px" height="'.$heigt.'px" '.$attrs.'/>';
        return $code;
    }

    public function logoExpandy()
    {
        $path = 'images/expandy/logo-expandy.png';
        return $this->img($path, 'Expandy', 'Expandy');
    }
    
    public function logoTextExpandy()
    {
        $path = 'images/expandy/logo-expandy.png';
        return $this->img($path, 'Expandy', 'Expandy');
    }

}