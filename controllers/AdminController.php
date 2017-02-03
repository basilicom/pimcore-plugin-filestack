<?php

use Pimcore\File;
use Pimcore\Tool;
use Pimcore\Model\Asset;
use Pimcore\Model\Element;
use Pimcore\Model;
use Pimcore\Logger;

class Filestack_AdminController extends \Pimcore\Controller\Action\Admin
{
    public function getapikeyAction()
    {
        $this->disableViewAutoRender();
        
        $config = new \Zend_Config_Xml(\Filestack\Plugin::getConfigName());
        $apiKey = $config->apiKey;        
        
        $this->_helper->json([
            "success" => true,
            "apiKey" => $apiKey
        ]);
    }

    public function uploadAction()
    {
        $this->disableViewAutoRender();
        
        $assetFolderId = $this->getParam("assetFolderId");
        $url = $this->getParam("url");
        $filename = $this->getParam("filename");
        
        try {

            $filename = Element\Service::getValidKey($filename, "asset");
            if (empty($filename)) {
                throw new \Exception("The filename of the asset is empty");
            }
            
            $parentAsset = Asset::getById(intval($assetFolderId));
            
            // check for duplicate filename
            $filename = $this->getSafeFilename($parentAsset->getRealFullPath(), $filename);

            $data = file_get_contents($url);

            $asset = Asset::create($assetFolderId, array(
                "filename" => $filename,
                //"type" => "folder",
                "userOwner" => $this->user->getId(),
                "userModification" => $this->user->getId()
            ));
            
            $asset->setData($data);
            $asset->save();

            $this->_helper->json([
                "success" => true
            ]);
        } catch (\Exception $e) {
            \Logger::err($e->getMessage());
            $this->_helper->json([
                "success" => false,
                "message" => $e->getMessage()
            ]);
        }
        
    }
    
    private function getSafeFilename($targetPath, $filename)
    {
        $originalFilename = $filename;
        $count = 1;
        if ($targetPath == "/") {
            $targetPath = "";
        }
        while (true) {
            if (Asset\Service::pathExists($targetPath . "/" . $filename)) {
                $filename = str_replace("." . File::getFileExtension($originalFilename), "_" . $count . "." . File::getFileExtension($originalFilename), $originalFilename);
                $count++;
            } else {
                return $filename;
            }
        }
    }
}
