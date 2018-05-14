<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\domain\entities\CProductSheetPrototype;
use bamboo\domain\entities\CProductSheetPrototypeHasProductDetailLabel;
use bamboo\domain\repositories\CProductDetailLabelRepo;
use function GuzzleHttp\Psr7\try_fopen;


/**
 * Class CProductSheetPrototypeManage
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 11/05/2018
 * @since 1.0
 */
class CProductSheetPrototypeManage extends AAjaxController
{
    /**
     * @return bool
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    public function post()
    {

       $pName = \Monkey::app()->router->request()->getRequestData('pName');
       $psp = \Monkey::app()->router->request()->getRequestData('psp');

       $checkArr = [];
       foreach ($psp as $val){
           $checkArr[] = $val['pr'];
       }

       $count = count($checkArr);
       $uniqueCount = count(array_unique($checkArr));
       if($count != $uniqueCount){
           return 'Hai inserito due priorità uguali';
       }

       /** @var CRepo $pspRepo */
       $pspRepo = \Monkey::app()->repoFactory->create('ProductSheetPrototype');


       //1
       /** @var CProductSheetPrototype $newPsp */
       $newPsp = $pspRepo->getEmptyEntity();
       $newPsp->name = $pName;
       $newPsp->smartInsert();


       //2
       /** @var CProductDetailLabelRepo $pdlRepo */
       $pdlRepo = \Monkey::app()->repoFactory->create('ProductDetailLabel');

       $ids = $pdlRepo->insertDetailLabel(1, $psp);

       /** @var CRepo $psphpdlRepo */
       $psphpdlRepo = \Monkey::app()->repoFactory->create('ProductSheetPrototypeHasProductDetailLabel');

       foreach ($ids as $id){
           /** @var CProductSheetPrototypeHasProductDetailLabel $newAssociation */
           $newAssociation = $psphpdlRepo->getEmptyEntity();
           $newAssociation->productSheetPrototypeId = $newPsp->id;
           $newAssociation->productDetailLabelId = $id;
           $newAssociation->smartInsert();
       }

       return 'Categorie inserite';
    }

}