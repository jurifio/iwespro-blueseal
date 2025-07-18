<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\domain\entities\CFoison;
use bamboo\domain\entities\CProductCardPhoto;
use bamboo\domain\entities\CProductSheetModelPrototypeCategoryGroup;
use bamboo\domain\repositories\CFoisonRepo;
use bamboo\ecommerce\views\VBase;
use bamboo\core\exceptions\RedPandaAssetException;
use bamboo\core\exceptions\RedPandaException;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\core\utils\amazonPhotoManager\ImageManager;
use bamboo\core\utils\amazonPhotoManager\S3Manager;

/**
 * Class CFoisonProfileImagePhotoAjaxManage
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 05/11/2018
 * @since 1.0
 */
class CFoisonProfileImagePhotoAjaxManage extends AAjaxController
{

    /**
     * @return bool|string
     * @throws RedPandaException
     * @throws \Exception
     * @throws \bamboo\core\exceptions\BambooException
     */
    public function post()
    {
        $foisonId = \Monkey::app()->router->request()->getRequestData('foisonId');

        /** @var CFoisonRepo $foisonRepo */
        $foisonRepo = \Monkey::app()->repoFactory->create('Foison');

        \Monkey::app()->vendorLibraries->load("amazon2723");
        $config = $this->app->cfg()->fetch('miscellaneous', 'amazonConfiguration');
        $tempFolder = $this->app->rootPath() . $this->app->cfg()->fetch('paths', 'tempFolder') . "/";

        $image = new ImageManager(new S3Manager($config['credential']), $this->app, $tempFolder);

        $numPhoto = count($_FILES['file']['name']);

        if($numPhoto > 1) return 'Puoi caricare una sola immagine di profilo';

        for ($i = 0; $i < $numPhoto; $i++) {
            if (!move_uploaded_file($_FILES['file']['tmp_name'][$i], $tempFolder . $_FILES['file']['name'][$i])) {
                throw new RedPandaException('Cannot move the uploaded Files');
            }
            /** @var CFoison $foison */
            $foison = $foisonRepo->findOneBy(['id' => $foisonId]);
            $fileName['name'] = $foison->userId . '_' . $foison->name . '_' . $foison->surname . '.' . explode('.', $_FILES['file']['name'][$i])[1];
            //$fileName['extension'] = pathinfo($_FILES['file']['name'][$i], PATHINFO_EXTENSION);


            try {
                $res = $image->processFoisonProfileImagePhoto($_FILES['file']['name'][$i], $fileName, $config['bucket'] . '-fason', 'profile-image');
            } catch (RedPandaAssetException $e) {
                $this->app->router->response()->raiseProcessingError();
                return 'Dimensioni della foto errate: il rapporto deve esser 9:16';
            }

            unlink($tempFolder . $_FILES['file']['name'][$i]);

            if ($res) {
                $url = "https://iwes-fason.s3-eu-west-1.amazonaws.com/profile-image/" . $fileName['name'];

                $foison->profileImageUrl = $url;
                $foison->update();
            }

        }

        return true;
    }
}