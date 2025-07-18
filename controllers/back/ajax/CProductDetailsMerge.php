<?php
namespace bamboo\controllers\back\ajax;

use bamboo\core\exceptions\BambooException;

/**
 * Class CProductListAjaxController
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Iwes  International Web Ecommerce ServicesTeam <juri@iwes.it>, ${DATE}
 *
 * @copyright (c) Iwes International Web Ecommerce Services - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */
class CProductDetailsMerge extends AAjaxController
{
    public function get()
    {
        $get = $this->app->router->request()->getRequestData();
        $prods = (array_key_exists('rows', $get)) ? $get['rows'] : [];
        $search = (array_key_exists('search', $get)) ? $get['search'] : false;

        //controllo size group e se ci sono ordini relativi ai prodotti da unire
        $selected = count($prods);
        $repoPro = \Monkey::app()->repoFactory->create('Product');
        $resProds = [];
        $resCount = 0;
        foreach ($prods as $k => $v) {
            if (!is_array($v)) {
                $p = [];
                list($p['id'], $p['productVariantId']) = explode('-', $v);
            } else {
                $p = $v;
            }
            $prod = $repoPro->findOneBy(['id' => $p['id'], 'productVariantId' => $p['productVariantId']]);
            if (!is_null($prod->productSheetPrototypeId)) {
                $resProds[$resCount]['code'] = $prod->id . "-" . $prod->productVariantId;
                $resProds[$resCount]['variant'] = $prod->productVariant->name;
                $resCount++;
            }
        }

        if ($search) {
            $code = explode('-', $search);
            $code['id'] = $code[0];
            unset($code[0]);
            if (array_key_exists(1, $code) && ("" !== $code[1])){
                $code['productVariantId'] = $code[1];
                unset($code[1]);
            }

            $prod = $repoPro->findBy($code);

            foreach($prod as $k => $v) {
                //throw new \Exception();
                if (!is_null($v->productSheetPrototypeId)) {
                    $resProds[$resCount]['code'] = $v->id . "-" . $v->productVariantId;
                    $resProds[$resCount]['variant'] = $v->productVariant->name;
                    $resCount++;
                }
            }
        }
        return json_encode($resProds);
    }

    public function post()
    {
        $get = $this->app->router->request()->getRequestData();
        $choosen = $this->getEntityByCode($get['choosen']);

        if (!$choosen) return "Attenzione! Il codice inserito non è un codice valido";

        $choosenPsa = \Monkey::app()->repoFactory->create('ProductSheetActual')->findBy(
            [
                'productId' => $choosen->id, 'productVariantId' => $choosen->productVariantId
            ]
        );
        try {
            \Monkey::app()->repoFactory->beginTransaction();
            foreach ($get['rows'] as $v) {
                if ($choosen->productVariantId == $v['productVariantId']) continue;

                $prod = \Monkey::app()->repoFactory->create('Product')->findOneBy(
                    [
                        'id' => $v['id'],
                        'productVariantId' => $v['productVariantId']
                    ]
                );
                $prod->productSheetPrototypeId = $choosen->productSheetPrototypeId;
                $prod->update();
                $prodName = \Monkey::app()->repoFactory->create('ProductNameTranslation')->findBy(
                    [
                        'productId' => $prod->id,
                        'productVariantId' => $prod->productVariantId,
                    ]
                );
                foreach($prodName as $name) {
                    $name->delete();
                }
                
                $choosenName = \Monkey::app()->repoFactory->create('ProductNameTranslation')->findBy(
                    [
                        'productId' => $choosen->id,
                        'productVariantId' => $choosen->productVariantId,
                    ]
                );

                foreach($choosenName as $name) {
                    $newProdName = \Monkey::app()->repoFactory->create('ProductNameTranslation')->getEmptyEntity();
                    $newProdName->productId = $prod->id;
                    $newProdName->productVariantId = $prod->productVariantId;
                    $newProdName->langId = $name->langId;
                    $newProdName->name = $name->name;
                    $newProdName->insert();
                }


                $prod->productNameTranslation = $choosen->productNameTranslation;

                $psa = \Monkey::app()->repoFactory->create('ProductSheetActual')->findBy(
                    [
                        'productId' => $prod->id, 'productVariantId' => $prod->productVariantId
                    ]
                );

                foreach ($psa as $psaSingle) {
                    $psaSingle->delete();
                }

                foreach ($choosenPsa as $cpsaSingle) {
                    $newPsa = \Monkey::app()->repoFactory->create('ProductSheetActual')->getEmptyEntity();
                    $newPsa->productId = $prod->id;
                    $newPsa->productVariantId = $prod->productVariantId;
                    $newPsa->productDetailLabelId = $cpsaSingle->productDetailLabelId;
                    $newPsa->productDetailId = $cpsaSingle->productDetailId;
                    $newPsa->insert();
                }

                foreach($prod->productHasProductCategory as $ppc) {
                    $ppc->delete();
                }
                $phpcRepo = \Monkey::app()->repoFactory->create('ProductHasProductCategory');
                foreach($choosen->productHasProductCategory as $pc) {
                    $ppc = $phpcRepo->getEmptyEntity();
                    $ppc->productId = $prod->id;
                    $ppc->productVariantId = $prod->productVariantId;
                    $ppc->productCategoryId = $pc->productCategoryId;
                    $ppc->insert();
                }
            }
            \Monkey::app()->repoFactory->commit();
        } catch (\Throwable $e) {
            \Monkey::app()->repoFactory->rollback();
            return 'OOPS! Si è verificato un problema:<br /> ' . $e->getMessage();
        }
        $res = 'I dettagli dei prodotti sono stati fusi correttamente.';

        return $res;
    }

    public function getEntityByCode($code, $single = true)
    {
        $position = strpos($code, "-");
        if ( (false === $position) || (0 === $position) || ( (strlen($code)-1) == $position) ) return false;
        list($id, $productVariantId) = explode('-', $code);
        if (!((is_numeric($id)) AND (is_numeric($productVariantId)))) return false;
        $ent = \Monkey::app()->repoFactory->create('Product');
        if ($single) {
            $res = $ent->findOneBy(['id' => $id, 'productVariantId' => $productVariantId]);
        } else {
            $res = $ent->findBy(['id' => $id, 'productVariantId' => $productVariantId]);
        }
        if (null === $res) return false;
        if (is_array($res) || $res instanceof Traversable)  {
            if (count($res)) return false;
        }
        return $res;
    }
}