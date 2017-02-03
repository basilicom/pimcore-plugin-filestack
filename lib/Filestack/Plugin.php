<?php

namespace Filestack;

use Pimcore\API\Plugin as PluginLib;

class Plugin extends PluginLib\AbstractPlugin implements PluginLib\PluginInterface
{

    const API_KEY_DUMMY = '__ADD_YOUR__FILESTACKP_KEY_HERE__';
    
    public function init()
    {
        parent::init();
    }

    public static function install()
    {
        $config = new \Zend_Config(array(), true);
        $config->apiKey = self::API_KEY_DUMMY;
        $configWriter = new \Zend_Config_Writer_Xml();
        $configWriter->setConfig($config);
        $configWriter->write(self::getConfigName());
        return 'Filestack plugin successfully installed';
    }
    
    public static function needsReloadAfterInstall() {
        return true; 
    }
    
    public static function uninstall()
    {
        if (file_exists(self::getConfigName())) {
            unlink(self::getConfigName());
        }
        return 'Filestack plugin successfully un-installed';
    }
    public static function isInstalled()
    {
        if (file_exists(self::getConfigName())) {
            return true;
        }
        return false;
    }
    public static function getConfigName()
    {
        return PIMCORE_WEBSITE_PATH
        . '/var/config/'
        . 'extension-'
        . str_replace('\plugin', '', strtolower(__CLASS__))
        . '.xml';
    }
}
