<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CProductDetail;
use bamboo\domain\entities\CProductDetailLabel;
use bamboo\domain\entities\CProductDetailTranslation;
use bamboo\domain\entities\CProductName;
use bamboo\domain\entities\CProductSheetActual;
use bamboo\domain\entities\CProductSheetModelActual;
use bamboo\domain\entities\CProductSheetModelPrototype;
use bamboo\domain\entities\CProductSheetModelPrototypeCategoryGroup;
use bamboo\domain\entities\CProductSheetModelPrototypeMacroCategoryGroup;
use bamboo\domain\repositories\CProductDetailRepo;

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
        if (empty($get['modelIds'])) {
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

                if (isset($get['genders'])) {
                    $newProt->genderId = $get['genders'];
                }

                if (isset($get['prodCats'])) {
                    $newProt->categoryGroupId = $get['prodCats'];
                }

                if (isset($get['materials'])) {
                    $newProt->materialId = $get['materials'];
                }

                if (isset($get['note'])) {
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

            $newIds = [];
            $mes = '';

            //Preparo l'inserimento
            foreach ($get['modelIds'][0]['res'] as $model) {
                try {
                    \Monkey::app()->repoFactory->beginTransaction();
                    $newName = str_ireplace($get['find-name'], $get['sub-name'], $model['name']);
                    $newCode = str_ireplace($get['find-code'], $get['sub-code'], $model['code']);

                    if (isset($get['find-product-name']) && isset($get['sub-product-name'])) {
                        //mi tiro fuori il nome composto e faccio lo stesso di prima -> ma se non c'è lo creoooo
                        $newProductName = str_ireplace(trim($get['find-product-name']), trim($get['sub-product-name']), $model['productName']);
                        $pn = \Monkey::app()->repoFactory->create('ProductNameTranslation')->findByName($newProductName);
                        if (!$pn) {
                            $exists = $this->isName($newProductName);

                            if (!$exists) {
                                $pntRepo = \Monkey::app()->repoFactory->create('ProductNameTranslation');
                                try {
                                    /** @var CProductName $newProdName */
                                    $newProdName = $pntRepo->insertName($newProductName);
                                } catch (\Throwable $e) {
                                    \Monkey::app()->repoFactory->rollback();
                                    return "OOPS! Errore durante l'inserimento, che non è stato eseguito.<br />" . $e->getMessage();
                                }
                            }
                        }
                    }

                    $newProt = \Monkey::app()->repoFactory->create('ProductSheetModelPrototype')->getEmptyEntity();
                    $newProt->productSheetPrototypeId = (!empty($productDetails) ? $productPrototypeId : $model['productSheetPrototypeId']);
                    $newProt->name = $newName;
                    $newProt->code = $newCode;

                    if (isset($get['find-product-name']) && isset($get['sub-product-name'])) {
                        if ($pn) {
                            $pnIt = $pn->findOneByKey('langId', 1);
                            $newProt->productName = str_replace(' !', '', $pnIt->name);
                        } else {
                            $newProt->productName = str_replace(' !', '', $newProdName->name);
                        }
                    } else {
                        $newProt->productName = $model['productName'];
                    }

                    $newProt->genderId = (!isset($get['genders']) ? $model['genderId'] : $get['genders']);


                    if (isset($get['prodCats'])) {
                        $newProt->categoryGroupId = $get['prodCats'];
                    } else if (!isset($get['prodCats']) && !isset($get['find-prodCats'])) {
                        $newProt->categoryGroupId = $model['categoryGroupId'];
                    } else if (!isset($get['prodCats']) && (isset($get['find-prodCats']) && isset($get['sub-prodCats']))) {

                        /** @var CRepo $exCatGroupRepo */
                        $exCatGroupRepo = \Monkey::app()->repoFactory->create('ProductSheetModelPrototypeCategoryGroup');
                        /** @var CProductSheetModelPrototypeCategoryGroup $extCatGroup */
                        $extCatGroup = $exCatGroupRepo->findOneBy(["id" => $model["categoryGroupId"]]);
                        $exCGName = $extCatGroup->name;

                        $newProdCats = str_ireplace($get['find-prodCats'], $get['sub-prodCats'], $exCGName);

                        $changeCatGroup = $exCatGroupRepo->findOneBy([
                            "name" => $newProdCats]);

                        if (!is_null($changeCatGroup)) {
                            $newProt->categoryGroupId = $changeCatGroup->id;
                        } else {
                            /** @var CProductSheetModelPrototypeCategoryGroup $newCategoryGroup */
                            $newCategoryGroup = $exCatGroupRepo->getEmptyEntity();
                            $newCategoryGroup->name = $newProdCats;
                            $newCategoryGroup->macroCategoryGroupId = CProductSheetModelPrototypeMacroCategoryGroup::DEFAULT;
                            $newCategoryGroup->smartInsert();

                            $newProt->categoryGroupId = $newCategoryGroup->id;
                        }

                    }


                    $newProt->materialId = (!isset($get['materials']) ? $model['materialId'] : $get['materials']);
                    $newProt->note = (!isset($get['note']) ? $model['note'] : $get['note']);
                    $newId = $newProt->insert();

                    $newIds[] = $newId;

                    //add cateogories
                    $em = $this->rfc('ProductSheetModelPrototypeHasProductCategory');

                    if (!empty($get['categories'])) {
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


                    /** @var CProductSheetModelPrototype $psmp */
                    $psmp = \Monkey::app()->repoFactory->create('ProductSheetModelPrototype')->findOneBy(['id' => $model['id']]);

                    /** @var CObjectCollection $psActualCollection */
                    $psActualCollection = $psmp->productSheetModelActual;

                    if ($get['Product_dataSheet'] == $model['productSheetPrototypeId']) {
                        if (!empty($productDetails)) {

                            $detailsToChange = $this->getDetailToChange($get, $psmp);

                            //mergio due array se definiti inserimenti
                            if (!empty($detailsToChange)) {
                                foreach ($detailsToChange as $k => $v) {
                                    $productDetails[$k] = $v;
                                }
                            }

                            //VECCHI
                            /** @var CProductSheetModelActual $psActual */
                            foreach ($psActualCollection as $psActual) {

                                //se ne trovo uno con la stessa etichetta di un nuovo salto il foreach
                                if (array_key_exists($psActual->productDetailLabelId, $productDetails)) continue;

                                $newSheet = \Monkey::app()->repoFactory->create('ProductSheetModelActual')->getEmptyEntity();
                                $newSheet->productSheetModelPrototypeId = $newId;
                                $newSheet->productDetailLabelId = $psActual->productDetailLabelId;
                                $newSheet->productDetailId = $psActual->productDetailId;
                                $newSheet->insert();
                            }

                            //NUOVI
                            foreach ($productDetails as $k => $v) {
                                $newSheet = \Monkey::app()->repoFactory->create('ProductSheetModelActual')->getEmptyEntity();
                                $newSheet->productSheetModelPrototypeId = $newId;
                                $newSheet->productDetailLabelId = $k;
                                $newSheet->productDetailId = $v;
                                $newSheet->insert();
                            }
                        } else {

                            //se non è cambiata e non è stato inserito nessun nuovo dettaglio
                            $detailsToChange = $this->getDetailToChange($get, $psmp);

                            /** @var CProductSheetActual $psActual */
                            foreach ($psActualCollection as $psActual) {

                                if (array_key_exists($psActual->productDetailLabelId, $detailsToChange)) {
                                    //se lo trovo nell'arry è custom e la prendo
                                    $labelDetailInsert = $detailsToChange[$psActual->productDetailLabelId];
                                } else {
                                    $labelDetailInsert = $psActual->productDetailId;
                                }

                                $newSheet = \Monkey::app()->repoFactory->create('ProductSheetModelActual')->getEmptyEntity();
                                $newSheet->productSheetModelPrototypeId = $newId;
                                $newSheet->productDetailLabelId = $psActual->productDetailLabelId;
                                $newSheet->productDetailId = $labelDetailInsert;
                                $newSheet->insert();
                            }

                        }
                    } else {
                        if (!empty($productDetails)) {
                            $this->insertDetails($productDetails, $newId, $newProt->productSheetPrototypeId);
                        } else {
                            throw new \Exception("E' necessario scrivere dettagli");
                        }
                    }


                    \Monkey::app()->repoFactory->commit();

                } catch (\Throwable $e) {
                    \Monkey::app()->repoFactory->rollback();
                    $mes .= '<br><br>'.$e->getMessage();
                }
            }
            return json_encode(['status' => 'new', 'productSheetModelPrototypeId' => json_encode($newIds), 'message' => json_encode($mes)]);


        }


    }

    public function put()
    {
        $get = $this->app->router->request()->getRequestData();

        if (isset($get['id'])) {
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

                $prot->productSheetPrototypeId = $pspid;

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

            $mes = '';
            $newIds = [];

            $productDetails = $this->getDetails($get);


            //Ciclo i modelli

            foreach ($get['modelIds'][0]['res'] as $model) {
                try {
                    \Monkey::app()->repoFactory->beginTransaction();
                    /** @var CProductSheetModelPrototype $psmp */
                    $psmp = \Monkey::app()->repoFactory->create('ProductSheetModelPrototype')->findOneBy(['id' => $model['id']]);

                    if (isset($get['find-name']) && isset($get['sub-name'])) $psmp->name = str_ireplace($get['find-name'], $get['sub-name'], $model['name']);
                    if (isset($get['find-code']) && isset($get['sub-code'])) $psmp->code = str_ireplace($get['find-code'], $get['sub-code'], $model['code']);
                    if ($get['Product_dataSheet'] != $model['productSheetPrototypeId']) $psmp->productSheetPrototypeId = $get['Product_dataSheet'];

                    if (isset($get['find-product-name']) && isset($get['sub-product-name'])) {
                        //mi tiro fuori il nome composto e faccio lo stesso di prima -> ma se non c'è lo creoooo
                        $newProductName = str_ireplace(trim($get['find-product-name']), trim($get['sub-product-name']), $model['productName']);
                        $pn = \Monkey::app()->repoFactory->create('ProductNameTranslation')->findByName($newProductName);
                        if (!$pn) {
                            $exists = $this->isName($newProductName);

                            if (!$exists) {
                                $pntRepo = \Monkey::app()->repoFactory->create('ProductNameTranslation');
                                try {
                                    /** @var CProductName $newProdName */
                                    $newProdName = $pntRepo->insertName($newProductName);
                                } catch (\Throwable $e) {
                                    \Monkey::app()->repoFactory->rollback();
                                    return "OOPS! Errore durante l'inserimento, che non è stato eseguito.<br />" . $e->getMessage();
                                }
                            }
                        }

                        if ($pn) {
                            $pnIt = $pn->findOneByKey('langId', 1);
                            $psmp->productName = str_replace(' !', '', $pnIt->name);
                        } else {
                            $psmp->productName = str_replace(' !', '', $newProdName->name);
                        }
                    }


                    if (isset($get['genders'])) $psmp->genderId = $get['genders'];
                    if (isset($get['prodCats'])) $psmp->categoryGroupId = $get['prodCats'];

                    if (isset($get['prodCats'])) {
                        $psmp->categoryGroupId = $get['prodCats'];
                    } else if (!isset($get['prodCats']) && (isset($get['find-prodCats']) && isset($get['sub-prodCats']))) {

                        /** @var CRepo $exCatGroupRepo */
                        $exCatGroupRepo = \Monkey::app()->repoFactory->create('ProductSheetModelPrototypeCategoryGroup');
                        /** @var CProductSheetModelPrototypeCategoryGroup $extCatGroup */
                        $extCatGroup = $exCatGroupRepo->findOneBy(["id" => $model["categoryGroupId"]]);
                        $exCGName = $extCatGroup->name;

                        $newProdCats = str_ireplace($get['find-prodCats'], $get['sub-prodCats'], $exCGName);

                        $changeCatGroup = $exCatGroupRepo->findOneBy([
                            "name" => $newProdCats]);

                        if (!is_null($changeCatGroup)) {
                            $psmp->categoryGroupId = $changeCatGroup->id;
                        } else {
                            /** @var CProductSheetModelPrototypeCategoryGroup $newCategoryGroup */
                            $newCategoryGroup = $exCatGroupRepo->getEmptyEntity();
                            $newCategoryGroup->name = $newProdCats;
                            $newCategoryGroup->macroCategoryGroupId = CProductSheetModelPrototypeMacroCategoryGroup::DEFAULT;
                            $newCategoryGroup->smartInsert();

                            $psmp->categoryGroupId = $newCategoryGroup->id;
                        }

                    }
                    if (isset($get['materials'])) $psmp->materialId = $get['materials'];
                    if (isset($get['note'])) $psmp->note = $get['note'];
                    $psmp->update();

                    if ($get['Product_dataSheet'] != $model['productSheetPrototypeId']) {
                        $oldSheets = \Monkey::app()->repoFactory->create('ProductSheetModelActual')->findBy(['productSheetModelPrototypeId' => $psmp->id]);

                        /** @var CProductSheetModelActual $pshma */
                        foreach ($oldSheets as $pshma) {
                            $pshma->delete();
                        }

                        if (!empty($productDetails)) {
                            $this->insertDetails($productDetails, $psmp->id, $psmp->productSheetPrototypeId);
                        } else {
                            throw new \Exception("E' necessario scrivere dettagli");
                        }
                    } else {
                        if (!empty($productDetails)) {

                            $detailsToChange = $this->getDetailToChange($get, $psmp);

                            //mergio due array se definiti inserimenti
                            if (!empty($detailsToChange)) {
                                foreach ($detailsToChange as $k => $v) {
                                    $productDetails[$k] = $v;
                                }
                            }

                            $oldSheets = \Monkey::app()->repoFactory->create('ProductSheetModelActual')->findBy(['productSheetModelPrototypeId' => $psmp->id]);

                            //VECCHI
                            /** @var CProductSheetModelActual $pshma */
                            foreach ($oldSheets as $pshma) {

                                //se ne trovo uno con la stessa etichetta di un nuovo salto il foreach
                                if (array_key_exists($pshma->productDetailLabelId, $productDetails)) {
                                    $pshma->delete();
                                }
                            }

                            //NUOVI
                            foreach ($productDetails as $k => $v) {
                                $newSheet = \Monkey::app()->repoFactory->create('ProductSheetModelActual')->getEmptyEntity();
                                $newSheet->productSheetModelPrototypeId = $psmp->id;
                                $newSheet->productDetailLabelId = $k;
                                $newSheet->productDetailId = $v;
                                $newSheet->insert();
                            }
                        } else {
                            $detailsToChange = $this->getDetailToChange($get, $psmp);

                            $oldSheets = \Monkey::app()->repoFactory->create('ProductSheetModelActual')->findBy(['productSheetModelPrototypeId' => $psmp->id]);

                            //VECCHI
                            /** @var CProductSheetModelActual $pshma */
                            foreach ($oldSheets as $pshma) {

                                if (array_key_exists($pshma->productDetailLabelId, $detailsToChange)) {
                                    $pshma->delete();
                                }
                            }

                            //NUOVI
                            foreach ($detailsToChange as $k => $v) {
                                $newSheet = \Monkey::app()->repoFactory->create('ProductSheetModelActual')->getEmptyEntity();
                                $newSheet->productSheetModelPrototypeId = $psmp->id;
                                $newSheet->productDetailLabelId = $k;
                                $newSheet->productDetailId = $v;
                                $newSheet->insert();
                            }

                        }
                    }

                    if (!empty($get['categories'])) {
                        $this->saveCats($get['categories'], $psmp->id);
                    }

                    $newIds[] = $psmp->id;
                    \Monkey::app()->repoFactory->commit();
                } catch (\Throwable $e) {
                    \Monkey::app()->repoFactory->rollback();
                    $mes .= '<br><br>' .$e->getMessage();
                }
            }
            return json_encode(['status' => 'updated', 'productSheetModelPrototypeId' => json_encode($newIds),  'message' => json_encode($mes)]);

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
    private function getDetails($get)
    {
        $productDetails = [];
        foreach ($get as $k => $v) {
            if (false !== strpos($k, 'ProductDetail')) {
                if ($v) $productDetails[explode('_', $k)[2]] = $v;
            }
        }
        return $productDetails;
    }

    private function getDetailToChange($get, CProductSheetModelPrototype $psmp)
    {
        $productDetails = [];
        foreach ($get as $k => $v) {
            if (false !== strpos($k, 'find-detail')) {
                if ($v) {
                    $productDetails[] = $v;
                } else {
                    return false;
                }
            } else if (false !== strpos($k, 'sub-detail')) {
                if ($v) {
                    $productDetails[] = $v;
                } else {
                    return false;
                }
            }
        }

        $detailsSingleValues = array_chunk($productDetails, 3);


        $res = [];
        /** @var CRepo $pntrRepo */
        $pntrRepo = \Monkey::app()->repoFactory->create('ProductDetailTranslation');
        foreach ($detailsSingleValues as $singleDetailV) {

            //se l'array non ha 3 elementi non sono stati assegnati valori
            if (count($singleDetailV) != 3) return [];

            /** @var CProductSheetActual $sheetActual */
            $sheetActual = $psmp->productSheetModelActual->findOneByKey('productDetailLabelId', $singleDetailV[0]);
            $detailTrans = $sheetActual->productDetail->productDetailTranslation->getFirst()->name;

            $newDetailName = str_ireplace($singleDetailV[1], $singleDetailV[2], $detailTrans);

            //cerco se ne esiste una con lo stesso nom in italian
            /** @var CProductDetailTranslation $existentLabel */
            $existentLabel = $pntrRepo->findOneBy(['name' => trim($newDetailName), 'langId' => 1]);
            if (!is_null($existentLabel)) {
                $res[$singleDetailV[0]] = $existentLabel->productDetail->id;
            } else {
                //inserisco una nuova label
                /** @var CProductDetailRepo $pdrepo */
                $pdrepo = \Monkey::app()->repoFactory->create('ProductDetail');

                /** @var CProductDetail $newOrFetchProductDetail */
                $newOrFetchProductDetail = $pdrepo->fetchOrInsert($newDetailName);
                $res[$singleDetailV[0]] = $newOrFetchProductDetail->id;
            }
        }

        return $res;
    }

    private function isName($name)
    {
        $pntRepo = \Monkey::app()->repoFactory->create('ProductNameTranslation');
        return $pn = $pntRepo->findByName($name);
    }

}