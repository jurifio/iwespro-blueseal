<?php
namespace bamboo\controllers\back\ajax;

use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\domain\entities\CProductCardPhoto;
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
class CEditorialPlanDetailImageUploadAjaxManage extends AAjaxController
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
        $tempFolder = $this->app->rootPath().$this->app->cfg()->fetch('paths', 'tempFolder').'-plandetail'."/";

        $image = new ImageManager(new S3Manager($config['credential']), $this->app, $tempFolder);

        $numPhoto = count($_FILES['file']['name']);

        for($i = 0; $i < $numPhoto; $i++){
            if (!move_uploaded_file($_FILES['file']['tmp_name'][$i], $tempFolder . $_FILES['file']['name'][$i])) {
                throw new RedPandaException('Cannot move the uploaded Files');
            }

            $fileName['name'] = explode('_', $_FILES['file']['name'][$i])[0];


            try{
                $res = $image->processImageEditorialUploadPhoto($_FILES['file']['name'][$i], $fileName, $config['bucket'].'-editorial', 'plandetail-images');
            }catch(RedPandaAssetException $e){
                $this->app->router->response()->raiseProcessingError();
                return 'Dimensioni della foto errate: il rapporto deve esser 9:16';
            }

          //  unlink($tempFolder . $_FILES['file']['name'][$i]);

            if($res){

                $url = "https://iwes-editorial.s3-eu-west-1.amazonaws.com/plandetail-images/".$fileName['name'];

            }

        }

        return $res;
    }

}