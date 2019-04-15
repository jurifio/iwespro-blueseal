<?php

namespace bamboo\controllers\back\ajax;
use bamboo\core\exceptions\RedPandaAssetException;
use bamboo\core\exceptions\RedPandaException;
use bamboo\core\utils\amazonPhotoManager\ImageManager;
use bamboo\core\utils\amazonPhotoManager\S3Manager;
use bamboo\domain\entities\CFixedPagePopup;


/**
 * Class CManageFixedPageAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 19/02/2019
 * @since 1.0
 */
class CManageFixedPagePhotoAjaxController extends AAjaxController
{
    /**
     * @return string
     * @throws RedPandaException
     * @throws \Exception
     * @throws \bamboo\core\exceptions\BambooException
     */
    public function post(){

        \Monkey::app()->vendorLibraries->load("amazon2723");
        $config = $this->app->cfg()->fetch('miscellaneous', 'amazonConfiguration');
        $tempFolder = $this->app->rootPath() . $this->app->cfg()->fetch('paths', 'tempFolder') . "/";
        $fixedPagePopupId = $this->app->router->request()->getRequestData('fixedPagePopupId');

        $image = new ImageManager(new S3Manager($config['credential']), $this->app, $tempFolder);

        $numPhoto = count($_FILES['file']['name']);

        for ($i = 0; $i < $numPhoto; $i++) {
            if (!move_uploaded_file($_FILES['file']['tmp_name'][$i], $tempFolder . $_FILES['file']['name'][$i])) {
                throw new RedPandaException('Cannot move the uploaded Files');
            }

            $fileName['name'] = explode('_', $_FILES['file']['name'][$i])[0];
            //$fileName['extension'] = pathinfo($_FILES['file']['name'][$i], PATHINFO_EXTENSION);

            try {
                $res = $image->processFixedPagePopupPhoto($_FILES['file']['name'][$i], $fileName, 'pickyshop', 'fixed-page');
            } catch (RedPandaAssetException $e) {
                $this->app->router->response()->raiseProcessingError();
                return 'Dimensioni della foto errate: il rapporto deve esser 9:16';
            }

            if ($res) {
                /** @var CFixedPagePopup $fixedPagePopup */
                $fixedPagePopup = \Monkey::app()->repoFactory->create('FixedPagePopup')->findOneBy(['id'=>$fixedPagePopupId]);
                $fixedPagePopup->img = $fileName['name'];
                $fixedPagePopup->update();
            }
        }
        return true;

    }
}