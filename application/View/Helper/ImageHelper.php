<?php

class View_Helper_ImageHelper
{
    function image($width, $height, $zoom, $quality, $sourceFilename)
    {
        include_once('phpThumb/phpthumb.class.php');
        error_reporting(E_WARNING);
        ini_set('display_errors', '1');

        $phpThumb = new phpThumb();
        $phpThumb->src = $sourceFilename;
        if ($width != null)
            $phpThumb->w = $width;
        if ($height != null)
            $phpThumb->h = $height;
        if ($zoom != null)
            $phpThumb->zc = $zoom;
        if ($quality != null)
            $phpThumb->q = $quality;
        $phpThumb->config_imagemagick_path = '/usr/bin/convert';
        $phpThumb->config_prefer_imagemagick = false;
        $phpThumb->config_output_format = 'jpg';
        $phpThumb->config_error_die_on_error = true;
        $phpThumb->config_document_root = '';
        $phpThumb->config_temp_directory = './temp/';
        $phpThumb->config_cache_directory = './cache/';
        $phpThumb->config_cache_disable_warning = true;
        $cacheFilename = md5($phpThumb->src);
        $phpThumb->cache_filename = $phpThumb->config_cache_directory . $cacheFilename . 'w' . $width . 'h' . $height . 'z' . $zoom . 'q' . $quality;
        
        if (!is_file($phpThumb->cache_filename))
        {            
            if (@getimagesize($sourceFilename))
            {                    
                if ($phpThumb->GenerateThumbnail())
                {                    
                    $phpThumb->RenderToFile($phpThumb->cache_filename);
                }
                else
                {
                    die('Failed: ' . $phpThumb->error);
                }
            }
            else
            { // Can't read source
                die("Couldn't read source image " . $sourceFilename);
            }
        }        
        
        
        if (is_file($phpThumb->cache_filename))
        { // If thumb was already generated we want to use cached version
            $cachedImage = getimagesize($phpThumb->cache_filename);            
            //header('Content-Type: ' . $cachedImage['mime']);
            readfile($phpThumb->cache_filename);
            exit;
        }
        else
        {
            readfile($sourceFilename);
            exit;
        }
    }
}
