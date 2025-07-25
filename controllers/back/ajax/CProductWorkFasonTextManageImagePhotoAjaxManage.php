<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\domain\entities\CFoison;
use bamboo\domain\entities\CProductBatch;
use bamboo\domain\entities\CProductBatchTextManage;
use bamboo\domain\entities\CProductBatchTextManagePhoto;
use bamboo\domain\entities\CProductCardPhoto;
use bamboo\domain\entities\CProductSheetModelPrototypeCategoryGroup;
use bamboo\domain\repositories\CFoisonRepo;
use bamboo\domain\repositories\CProductBatchRepo;
use bamboo\domain\repositories\CProductBatchTextManageRepo;
use bamboo\ecommerce\views\VBase;
use bamboo\core\exceptions\RedPandaAssetException;
use bamboo\core\exceptions\RedPandaException;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\core\utils\amazonPhotoManager\ImageManager;
use bamboo\core\utils\amazonPhotoManager\S3Manager;

/**
 * Class CProductWorkFasonTextManageImagePhotoAjaxManage
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 04/12/2018
 * @since 1.0
 */
class CProductWorkFasonTextManageImagePhotoAjaxManage extends AAjaxController
{

    /**
     * @return bool|string
     * @throws RedPandaException
     * @throws \Exception
     * @throws \bamboo\core\exceptions\BambooException
     */
    public function post()
    {
        $productBatchTextManageId = \Monkey::app()->router->request()->getRequestData('productBatchTextManageId');
        $txt = \Monkey::app()->router->request()->getRequestData('txt');

        if(empty($txt)) return 'Non hai inserito nessun testo';

        /** @var CProductBatchTextManage $productBatchTextManage */
        $productBatchTextManage = \Monkey::app()->repoFactory->create('ProductBatchTextManage')->findOneBy(['id'=>$productBatchTextManageId]);

        \Monkey::app()->vendorLibraries->load("amazon2723");
        $config = $this->app->cfg()->fetch('miscellaneous', 'amazonConfiguration');
        $tempFolder = $this->app->rootPath() . $this->app->cfg()->fetch('paths', 'tempFolder') . "/";

        $image = new ImageManager(new S3Manager($config['credential']), $this->app, $tempFolder);

        $numPhoto = count($_FILES['file']['name']);

        for ($i = 0; $i < $numPhoto; $i++) {
            if (!move_uploaded_file($_FILES['file']['tmp_name'][$i], $tempFolder . $_FILES['file']['name'][$i])) {
                throw new RedPandaException('Cannot move the uploaded Files');
            }

            $fileName['name'] = $productBatchTextManage->productBatchId . '.' . $productBatchTextManage->id . '.' . $_FILES['file']['name'][$i];
            //$fileName['extension'] = pathinfo($_FILES['file']['name'][$i], PATHINFO_EXTENSION);


            try {
                $res = $image->processProductBatchTextManageImagePhoto($_FILES['file']['name'][$i], $fileName, $config['bucket'] . '-fason', 'text-manage-photo/post-worked-image');
            } catch (RedPandaAssetException $e) {
                $this->app->router->response()->raiseProcessingError();
                return 'Dimensioni della foto errate: il rapporto deve esser 9:16';
            }

            unlink($tempFolder . $_FILES['file']['name'][$i]);

            if ($res) {

                /** @var CProductBatchTextManagePhoto $productBatchTextManagePhoto */
                $productBatchTextManagePhoto = \Monkey::app()->repoFactory->create('ProductBatchTextManagePhoto')->getEmptyEntity();
                $productBatchTextManagePhoto->imageName = $fileName['name'];
                $productBatchTextManagePhoto->productBatchTextManageId = $productBatchTextManage->id;
                $productBatchTextManagePhoto->isDummy = 0;
                $productBatchTextManagePhoto->smartInsert();

                $productBatchTextManage->descriptionFason = $txt;
                $productBatchTextManage->update();
            }

        }

        return 'Il testo è stato inserito con successo';
    }
}