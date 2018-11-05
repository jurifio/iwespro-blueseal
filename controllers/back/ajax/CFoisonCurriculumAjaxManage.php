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
class CFoisonCurriculumAjaxManage extends AAjaxController
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

        /** @var CFoison $foison */
        $foison = \Monkey::app()->repoFactory->create('Foison')->findOneBy(['id'=>$foisonId]);
        $cont = file_get_contents($_FILES['file']['tmp_name'][0]);

        $foison->curriculum = $cont;
        $foison->update();

        return true;
    }
}