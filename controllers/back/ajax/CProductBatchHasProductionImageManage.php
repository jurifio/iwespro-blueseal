<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CContractDetails;
use bamboo\domain\entities\CFoison;
use bamboo\domain\entities\CProductBatchHasProductionImage;
use bamboo\domain\entities\CProductBatchDetails;
use bamboo\domain\entities\CProductSizeGroup;
use bamboo\domain\entities\CProductSizeMacroGroup;
use bamboo\domain\entities\CUser;
use bamboo\domain\repositories\CContractDetailsRepo;
use bamboo\domain\repositories\CContractsRepo;
use bamboo\domain\repositories\CEmailRepo;
use bamboo\domain\repositories\CFoisonRepo;
use bamboo\domain\repositories\CProductBatchDetailsRepo;
use bamboo\domain\repositories\CProductBatchRepo;
use bamboo\domain\repositories\CProductSizeGroupRepo;
use bamboo\domain\repositories\CProductSizeRepo;
use bamboo\domain\repositories\CSectionalRepo;
use bamboo\domain\repositories\CUserRepo;


/**
 * Class CProductBatchHasProductionImageManage
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 28/10/2020
 * @since 1.0
 */
class CProductBatchHasProductionImageManage extends AAjaxController
{
    /**
     * @return string
     * @throws BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function post()
    {

    }

    /**
     * @return string
     */
    public function get()
    {

    }

    /**
     * @throws BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function put()
    {

    }

    public function delete()
    {
        $products = \Monkey::app()->router->request()->getRequestData('products');
        $productBatchId = \Monkey::app()->router->request()->getRequestData('productBatchId');


        /** @var CProductBatchHasProductionImage $pbdRepo */
        $pbdRepo = \Monkey::app()->repoFactory->create('ProductBatchHasProductionImage');
        $file = '';
        foreach ($products as $product) {
            $deleteImage = $pbdRepo->findOneBy(['id' => $product,'productBatchId' => $productBatchId]);

            if (ENV == 'dev') {
                $pathFileDest = '/media/sf_sites/iwespro/client/public/media/folderImages/' . $deleteImage->imageName;
            } else {
                $pathFileDest = '/home/iwespro/public_html/client/public/media/folderImages/' . $deleteImage->imageName;

            }
            unlink($pathFileDest);
            $deleteImage->delete();
        }

        if ($d) return 'Immagini eliminate con successo dal lotto';


    }

}