<?php
namespace bamboo\controllers\back\ajax;

use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\domain\entities\CProductCardPhoto;
use bamboo\domain\entities\CProductSheetModelPrototypeCategoryGroup;
use bamboo\domain\entities\CProductSheetModelPrototypeMacroCategoryGroup;
use bamboo\ecommerce\views\VBase;
use bamboo\core\exceptions\RedPandaAssetException;
use bamboo\core\exceptions\RedPandaException;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\core\utils\amazonPhotoManager\ImageManager;
use bamboo\core\utils\amazonPhotoManager\S3Manager;

/**
 * Class CProductModelPrototypeMacroCategoryPhotoAjaxManage
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 14/06/2018
 * @since 1.0
 */
class CProductModelPrototypeMacroCategoryPhotoAjaxManage extends AAjaxController
{

    /**
     * @return bool|string
     * @throws RedPandaException
     * @throws \Exception
     * @throws \bamboo\core\exceptions\BambooException
     */
    public function post()
    {
        $macroCatIds = \Monkey::app()->router->request()->getRequestData('macroCatIds');


        /** @var CRepo $prodMacroCatGroupRepo */
        $prodMacroCatGroupRepo = \Monkey::app()->repoFactory->create('ProductSheetModelPrototypeMacroCategoryGroup');


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
            //$fileName['extension'] = pathinfo($_FILES['file']['name'][$i], PATHINFO_EXTENSION);


            try{
                $res = $image->processProductModelPrototypeCategoryGroupPhoto($_FILES['file']['name'][$i], $fileName, $config['bucket'].'-fason', 'model-prototype-macro-category');
            }catch(RedPandaAssetException $e){
                $this->app->router->response()->raiseProcessingError();
                return 'Dimensioni della foto errate: il rapporto deve esser 9:16';
            }

            //unlink($tempFolder . $_FILES['file']['name'][$i]);

            if($res){
                $macroIds = explode(',', $macroCatIds);
                $url = "https://iwes-fason.s3-eu-west-1.amazonaws.com/model-prototype-macro-category/".$fileName['name'];
                foreach ($macroIds as $macroCatId) {
                    /** @var CProductSheetModelPrototypeMacroCategoryGroup $prodMacroCatPhoto */
                    $prodMacroCatPhoto = $prodMacroCatGroupRepo->findOneBy([
                        'id'=>$macroCatId
                    ]);
                    $prodMacroCatPhoto->imageUrl = $url;
                    $prodMacroCatPhoto->update();
                }
            }

        }

        return true;
    }

}