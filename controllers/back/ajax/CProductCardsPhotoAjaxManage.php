<?php
namespace bamboo\controllers\back\ajax;

use bamboo\ecommerce\views\VBase;
use bamboo\core\exceptions\RedPandaAssetException;
use bamboo\core\exceptions\RedPandaException;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\core\utils\amazonPhotoManager\ImageManager;
use bamboo\core\utils\amazonPhotoManager\S3Manager;

/**
 * Class CProductCardsPhotoAjaxManage
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 09/05/2018
 * @since 1.0
 */
class CProductCardsPhotoAjaxManage extends AAjaxController
{

    /**
     * @return bool|string
     * @throws RedPandaException
     * @throws \Exception
     * @throws \bamboo\core\exceptions\BambooException
     */
    public function post()
    {

        \Monkey::app()->vendorLibraries->load("amazon2723");
        $config = $this->app->cfg()->fetch('miscellaneous', 'amazonConfiguration');
        $tempFolder = $this->app->rootPath().$this->app->cfg()->fetch('paths', 'tempFolder')."/";

        $image = new ImageManager(new S3Manager($config['credential']), $this->app, $tempFolder);

        $numPhoto = count($_FILES['file']['name']);

        for($i = 0; $i < $numPhoto; $i++){
            if (!move_uploaded_file($_FILES['file']['tmp_name'][$i], $tempFolder . $_FILES['file']['name'][$i])) {
                throw new RedPandaException('Cannot move the uploaded Files');
            }

            $fileName['name'] = explode('_', $_FILES['file']['name'][$i])[0];
            $fileName['extension'] = pathinfo($_FILES['file']['name'][$i], PATHINFO_EXTENSION);


            try{
                $res = $image->processProductCardsPhoto($_FILES['file']['name'][$i], $fileName, $config['bucket'].'-fason', 'product-cards');
            }catch(RedPandaAssetException $e){
                $this->app->router->response()->raiseProcessingError();
                return 'Dimensioni della foto errate: il rapporto deve esser 9:16';
            }

            unlink($tempFolder . $_FILES['file']['name'][$i]);

        }




        /*
            if (!move_uploaded_file($_FILES['file']['tmp_name'], $tempFolder . $_FILES['file']['name'])) {
                throw new RedPandaException('Cannot move the uploaded Files');
            }



            $fileName['name'] = $product->printId();
            $fileName['number'] = (string) str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);
            $fileName['extension'] = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);

            try{
                $res = $image->process($_FILES['file']['name'], $fileName, $config['bucket'].'-fason', 'product-cards');
            }catch(RedPandaAssetException $e){
                $this->app->router->response()->raiseProcessingError();
                return 'Dimensioni della foto errate: il rapporto deve esser 9:16';
            }

            $futureDummy = "";
            if (count($res) < 4) {
                //error
            } else {
                foreach ($res as $key => $val) {
                    if (empty($futureDummy)) {
                        $futureDummy = $val;
                    }
                    $ids[] = $this->app->dbAdapter->insert('ProductPhoto', array('name' => $val, 'order' => $fileName['number'], 'size' => $key));
                }
                unlink($tempFolder . $_FILES['file']['name']);
                $count = 0;
                foreach ($ids as $key => $val) {
                    $this->app->dbAdapter->insert("ProductHasProductPhoto", ["productId" => $product->id, "productVariantId" => $product->productVariantId, "productPhotoId" => $val]);
                    $count++;
                }
            }
            */

        return true;
    }

}