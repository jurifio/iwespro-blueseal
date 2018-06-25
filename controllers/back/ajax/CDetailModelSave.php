<?php
namespace bamboo\controllers\back\ajax;
use bamboo\core\base\CObjectCollection;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CProductSheetActual;
use bamboo\domain\entities\CProductSheetModelActual;
use bamboo\domain\entities\CProductSheetModelPrototype;

/**
 * Class CProductListAjaxController
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>, ${DATE}
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */
class CDetailModelSave extends AAjaxController
{
    public function get()
    {
        // nothing to do here
    }

    public function post()
    {
        $get = $this->app->router->request()->getRequestData();

        $productPrototypeId = $get['Product_dataSheet'];
        $productDetails = $this->getDetails($get);

        //IF IS MULTIPLE CREATION OR !
        if(empty($get['modelIds'])){
            try {
                \Monkey::app()->repoFactory->beginTransaction();

                $pn = \Monkey::app()->repoFactory->create('ProductNameTranslation')->findByName(trim($get['productName']));
                if (!$pn) throw new BambooException('Non si può creare un modello con un nome prodotto inesistente');
                $pnIt = $pn->findOneByKey('langId', 1);
                $newProt = \Monkey::app()->repoFactory->create('ProductSheetModelPrototype')->getEmptyEntity();
                $newProt->productSheetPrototypeId = $productPrototypeId;
                $newProt->name = $get['name'];
                $newProt->code = $get['code'];
                $newProt->productName = str_replace(' !', '', $pnIt->name);

                if(isset($get['genders'])){
                   $newProt->genderId = $get['genders'];
                }

                if(isset($get['prodCats'])) {
                    $newProt->categoryGroupId = $get['prodCats'];
                }

                if(isset($get['materials'])) {
                    $newProt->materialId = $get['materials'];
                }

                if(isset($get['note'])) {
                    $newProt->note = $get['note'];
                }


                $newId = $newProt->insert();

                $this->saveCats($get['categories'], $newId);

                $this->insertDetails($productDetails, $newId, $productPrototypeId);
                \Monkey::app()->repoFactory->commit();

                return json_encode(['status' => 'new', 'productSheetModelPrototypeId' => $newId]);
            } catch (\Throwable $e) {
                \Monkey::app()->repoFactory->rollback();
                return json_encode(['status' => "ko", 'message' => $e->getMessage()]);
            }
        } else {
            try {
                $newIds = [];
                \Monkey::app()->repoFactory->beginTransaction();

                //Preparo l'inserimento
                foreach ($get['modelIds'][0]['res'] as $model){

                    $newName = str_ireplace($get['find-name'], $get['sub-name'], $model['name']);
                    $newCode = str_ireplace($get['find-code'], $get['sub-code'], $model['code']);

                    $pn = \Monkey::app()->repoFactory->create('ProductNameTranslation')->findByName(trim($get['productName']));
                    if (!$pn) throw new BambooException('Non si può creare un modello con un nome prodotto inesistente');
                    $pnIt = $pn->findOneByKey('langId', 1);
                    $newProt = \Monkey::app()->repoFactory->create('ProductSheetModelPrototype')->getEmptyEntity();
                    $newProt->productSheetPrototypeId = (!empty($productDetails) ? $productPrototypeId : $model['productSheetPrototypeId']);
                    $newProt->name = $newName;
                    $newProt->code = $newCode;
                    $newProt->productName = str_replace(' !', '', $pnIt->name);
                    $newProt->genderId = (!isset($get['genders']) ? $model['genderId'] : $get['genders']);
                    $newProt->categoryGroupId =  (!isset($get['prodCats']) ? $model['categoryGroupId'] : $get['prodCats']);
                    $newProt->materialId =  (!isset($get['materials']) ? $model['materialId'] : $get['materials']);
                    $newProt->note =  (!isset($get['note']) ? $model['note'] : $get['note']);
                    $newId = $newProt->insert();

                    $newIds[] = $newId;

                    //add cateogories
                    $em = $this->rfc('ProductSheetModelPrototypeHasProductCategory');

                    if(!empty($get['categories'])) {
                        if (!is_array($get['categories'])) throw new \Exception('$cats must be an array');
                        foreach ($get['categories'] as $v) {
                            $cat = $em->getEmptyEntity();
                            $cat->productSheetModelPrototypeId = $newId;
                            $cat->productCategoryId = $v;
                            $cat->insert();
                        }
                    } else {
                        foreach ($model['categories'] as $v) {
                            $cat = $em->getEmptyEntity();
                            $cat->productSheetModelPrototypeId = $newId;
                            $cat->productCategoryId = $v;
                            $cat->insert();
                        }
                    }


                    if(!empty($productDetails)){
                        foreach ($productDetails as $k => $v) {
                            $newSheet = \Monkey::app()->repoFactory->create('ProductSheetModelActual')->getEmptyEntity();
                            $newSheet->productSheetModelPrototypeId = $newId;
                            $newSheet->productDetailLabelId = $k;
                            $newSheet->productDetailId = $v;
                            $newSheet->insert();
                        }
                    } else {
                        /** @var CProductSheetModelPrototype $psmp */
                        $psmp = \Monkey::app()->repoFactory->create('ProductSheetModelPrototype')->findOneBy(['id'=>$model['id']]);

                        /** @var CObjectCollection $psActualCollection */
                        $psActualCollection = $psmp->productSheetModelActual;

                        /** @var CProductSheetActual $psActual */
                        foreach ($psActualCollection as $psActual){
                            $newSheet = \Monkey::app()->repoFactory->create('ProductSheetModelActual')->getEmptyEntity();
                            $newSheet->productSheetModelPrototypeId = $newId;
                            $newSheet->productDetailLabelId = $psActual->productDetailLabelId;
                            $newSheet->productDetailId = $psActual->productDetailId;
                            $newSheet->insert();
                        }

                    }


                }
                \Monkey::app()->repoFactory->commit();
                return json_encode(['status' => 'new', 'productSheetModelPrototypeId' => json_encode($newIds)]);

            } catch (\Throwable $e) {
                \Monkey::app()->repoFactory->rollback();
                return json_encode(['status' => "ko", 'message' => $e->getMessage()]);
            }
        }


    }

    public function put()
    {
        $get = $this->app->router->request()->getRequestData();

        if(isset($get['id'])) {
            $id = $get['id'];
            $pspid = $get['Product_dataSheet'];

            $productDetails = $this->getDetails($get);

            $prot = \Monkey::app()->repoFactory->create('ProductSheetModelPrototype')->findOneBy(['id' => $id]);

            try {
                \Monkey::app()->repoFactory->beginTransaction();

                //update model

                $pn = \Monkey::app()->repoFactory->create('ProductNameTranslation')->findByName(trim($get['productName']));
                if (!$pn) throw new BambooException('Non si può aggiornare un modello con un nome prodotto inesistente');
                $pnIt = $pn->findOneByKey('langId', 1);
                $prot->name = $get['name'];
                if ($get['code']) {
                    $prot->code = $get['code'];
                }
                if ($get['productName']) {
                    $prot->productName = str_replace(' !', '', $pnIt->name);
                }

                if (isset($get['genders'])) $prot->genderId = $get['genders'];


                if (isset($get['prodCats'])) $prot->categoryGroupId = $get['prodCats'];

                if (isset($get['materials'])) $prot->materialId = $get['materials'];

                $prot->update();
                //delete all details associated to this model
                $psma = $prot->productSheetModelActual;
                foreach ($psma as $v) {
                    $v->delete();
                }
                //insert new details
                $this->insertDetails($productDetails, $prot->id, $prot->productSheetPrototypeId);

                $this->saveCats($get['categories'], $prot->id);
                \Monkey::app()->repoFactory->commit();
            } catch (\Throwable $e) {
                \Monkey::app()->repoFactory->rollback();
                return json_encode(['status' => "ko", 'message' => $e->getMessage()]);
            }
            $res = ['status' => 'ok', 'productSheetModelPrototypeId' => $prot->id];
            return json_encode($res);
        } else {

            try {
                $newIds = [];

                $productDetails = $this->getDetails($get);

                \Monkey::app()->repoFactory->beginTransaction();

                //Ciclo i modelli
                foreach ($get['modelIds'][0]['res'] as $model) {


                    /** @var CProductSheetModelPrototype $psmp */
                    $psmp = \Monkey::app()->repoFactory->create('ProductSheetModelPrototype')->findOneBy(['id'=>$model['id']]);

                    $psmp->name = str_ireplace($get['find-name'], $get['sub-name'], $model['name']);
                    $psmp->code = str_ireplace($get['find-code'], $get['sub-code'], $model['code']);
                    if($get['Product_dataSheet'] != $model['productSheetPrototypeId']) $psmp->productSheetPrototypeId = $get['Product_dataSheet'];
                    if(isset($get['productName'])) $psmp->productName = $get['productName'];
                    if(isset($get['genders'])) $psmp->genderId = $get['genders'];
                    if(isset($get['prodCats'])) $psmp->categoryGroupId = $get['prodCats'];
                    if(isset($get['materials'])) $psmp->materialId = $get['materials'];
                    if(isset($get['note'])) $psmp->note = $get['note'];
                    $psmp->update();

                    if($get['Product_dataSheet'] != $model['productSheetPrototypeId']) {
                        $oldSheets = \Monkey::app()->repoFactory->create('ProductSheetModelActual')->findBy(['productSheetModelPrototypeId'=>$psmp->id]);

                        /** @var CProductSheetModelActual $pshma */
                        foreach ($oldSheets as $pshma){
                            $pshma->delete();
                        }

                        if (!empty($productDetails)) {
                            $this->insertDetails($productDetails, $psmp->id, $psmp->productSheetPrototypeId);
                        } else {
                            throw new \Exception("E' necessario scrivere dettagli");
                        }
                    } else {
                        if (!empty($productDetails)) {
                            $oldSheets = \Monkey::app()->repoFactory->create('ProductSheetModelActual')->findBy(['productSheetModelPrototypeId'=>$psmp->id]);

                            /** @var CProductSheetModelActual $pshma */
                            foreach ($oldSheets as $pshma){
                                $pshma->delete();
                            }

                            $this->insertDetails($productDetails, $psmp->id, $psmp->productSheetPrototypeId);
                        }
                    }

                    if(!empty($get['categories'])) {
                        $this->saveCats($get['categories'], $psmp->id);
                    }

                    $newIds[] = $psmp->id;
                }
                \Monkey::app()->repoFactory->commit();
                return json_encode(['status' => 'new', 'productSheetModelPrototypeId' => json_encode($newIds)]);

            } catch (\Throwable $e) {
                \Monkey::app()->repoFactory->rollback();
                return json_encode(['status' => "ko", 'message' => $e->getMessage()]);
            }

        }
    }

    /**
     * @param $productDetails
     * @param $productSheetModelPrototypeId
     * @param $productSheetPrototypeId
     */
    private function insertDetails($productDetails, $productSheetModelPrototypeId, $productSheetPrototypeId)
    {
        foreach ($productDetails as $k => $v) {
            $newSheet = \Monkey::app()->repoFactory->create('ProductSheetModelActual')->getEmptyEntity();
            $newSheet->productSheetModelPrototypeId = $productSheetModelPrototypeId;
            $newSheet->productDetailLabelId = $k;
            $newSheet->productDetailId = $v;
            $newSheet->insert();
        }
    }



    /**
     * @param $cats
     * @param $modelId
     * @throws \Exception
     */
    private function saveCats($cats, $modelId)
    {
        $em = $this->rfc('ProductSheetModelPrototypeHasProductCategory');
        if (!is_array($cats)) throw new \Exception('$cats must be an array');

        $catDel = $em->findBy(['productSheetModelPrototypeId' => $modelId]);
        foreach ($catDel as $v) {
            $v->delete();
        }

        foreach ($cats as $v) {
            $cat = $em->getEmptyEntity();
            $cat->productSheetModelPrototypeId = $modelId;
            $cat->productCategoryId = $v;
            $cat->insert();
        }
    }

    /**
     * @param $get
     */
    private function getDetails($get) {
        $productDetails = [];
        foreach ($get as $k => $v) {
            if (false !== strpos($k, 'ProductDetail')) {
                if ($v) $productDetails[explode('_', $k)[2]] = $v;
            }
        }
        return $productDetails;
    }

}