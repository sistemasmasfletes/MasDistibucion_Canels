<?php
class Plugin_Translate extends Model3_Plugin
{    
    /**
     *
     * @param Model3_Request $request 
     */
    public function onPreDispatch($request)
    {
        $config = Model3_Registry::get('config');
        $configData = $config->getArray();
        
        $translate = new Zend_Translate('gettext', $configData['m3_internationalization']['inter_dir_lang'] .'es.mo', 'es');
        $translate->addTranslation(array('content' => $configData['m3_internationalization']['inter_dir_lang'].'en.mo', 'locale' => 'en'));
        
        //var_dump($request->getLang());
        $translate->setLocale($request->getLang());
        
        Model3_Registry::set('translate', $translate);
    }
    
}
