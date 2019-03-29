<?php

namespace bamboo\controllers\back\ajax;
use bamboo\domain\entities\CPrestashopHasProduct;
use bamboo\domain\repositories\CPrestashopHasProductRepo;


/**
 * Class CPrestashopHasProductManageWithCron
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 29/03/2019
 * @since 1.0
 */
class CPrestashopHasProductManageWithCron extends AAjaxController
{
    public function get()
    {
    }

    /**
     * @throws \bamboo\core\exceptions\BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function post()
    {
        /** @var CPrestashopHasProductRepo $phpRepo */
        $phpRepo = \Monkey::app()->repoFactory->create('PrestashopHasProduct');

        foreach ($this->data['products'] as $productCode){

            $productIds = explode('-',$productCode);

            /** @var CPrestashopHasProduct $php */
            $php = $phpRepo->findOneBy(['productId'=>$productIds[0], 'productVariantId'=>$productIds[1]]);
            $php->marketplaceHasShopId = $this->data['marketplaceHasShopId'];
            $php->modifyType = $this->data['modifyType'];
            $php->variantValue = $this->data['variantValue'];
            $php->update();
        }

        return 'Prodotti prenotati con successo';
    }


    public function put()
    {
    }

    public function delete()
    {
    }

}