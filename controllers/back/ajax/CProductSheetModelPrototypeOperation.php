<?php

namespace bamboo\controllers\back\ajax;
use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\domain\entities\CProductDetailLabel;
use bamboo\domain\entities\CProductDetailLabelTranslation;
use bamboo\domain\entities\CProductSheetModelPrototype;
use bamboo\domain\entities\CProductSheetPrototype;
use bamboo\domain\entities\CProductSheetPrototypeHasProductDetailLabel;
use bamboo\domain\repositories\CProductDetailLabelRepo;


/**
 * Class CProductSheetModelPrototypeOperation
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 29/05/2018
 * @since 1.0
 */
class CProductSheetModelPrototypeOperation extends AAjaxController
{
    /**
     * @return string
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    public function post(){
        $id = \Monkey::app()->router->request()->getRequestData('id');
        $newName = \Monkey::app()->router->request()->getRequestData('newName');

        /** @var CRepo $pspRepo */
        $pspRepo = \Monkey::app()->repoFactory->create('ProductSheetPrototype');


        /** @var CProductSheetPrototype $psp */
        $psp = $pspRepo->findOneBy(['id'=>$id]);

        /** @var CProductSheetPrototype $newPsp */
        $newPsp = $pspRepo->getEmptyEntity();
        $newPsp->name = $newName;
        $newPsp->smartInsert();

        //inserisco detail label
        /** @var CObjectCollection $pdls */
        $pdls = $psp->productDetailLabel;

        //Preparo l'array per passarlo alla funzione che crea nuovi dettagli uguali ma con id diverso

        $pspNew = [];
        $i = 0;
        /** @var CProductDetailLabel $pdl */
        foreach ($pdls as $pdl){
            $name = $pdl->productDetailLabelTranslation->findOneByKey('langId',1)->name;

            $pspNew[$i]['name'] = $name;
            $pspNew[$i]['pr'] = $pdl->order;
            $i++;
        }


        /** @var CProductDetailLabelRepo $pdlRepo */
        $pdlRepo = \Monkey::app()->repoFactory->create('ProductDetailLabel');
        $ids = $pdlRepo->insertDetailLabel(1, $pspNew);


        //inserisco l associazione
        /** @var CRepo $psphpdlRepo */
        $psphpdlRepo = \Monkey::app()->repoFactory->create('ProductSheetPrototypeHasProductDetailLabel');

        foreach ($ids as $id){
            /** @var CProductSheetPrototypeHasProductDetailLabel $newAssociation */
            $newAssociation = $psphpdlRepo->getEmptyEntity();
            $newAssociation->productSheetPrototypeId = $newPsp->id;
            $newAssociation->productDetailLabelId = $id;
            $newAssociation->smartInsert();
        }

        return 'Il modello è stato clonato con successo';

    }


    /**
     * @return string
     * @throws \bamboo\core\exceptions\BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function put()
    {
        $ids = \Monkey::app()->router->request()->getRequestData('ids');

        /** @var CRepo $pspRepo */
        $pspRepo = \Monkey::app()->repoFactory->create('ProductSheetPrototype');

        foreach ($ids as $id) {

            /** @var CProductSheetPrototype $psp */
            $psp = $pspRepo->findOneBy(['id'=>$id]);
            $psp->isVisible = 0;
            $psp->update();

        }


        return 'Modelli nascosti con successo';


    }

}