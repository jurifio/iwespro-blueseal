<?php
namespace bamboo\blueseal\controllers\ajax;

use bamboo\core\exceptions\BambooException;

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
class CProductDetailsMerge extends AAjaxController
{
    public function get()
    {
        $get = $this->app->router->request()->getRequestData();
        $prods = $get['rows'];


        //controllo size group e se ci sono ordini relativi ai prodotti da unire
        $selected = count($prods);
        $repoPro = $this->app->repoFactory->create('Product');
        $resProds = [];
        foreach ($prods as $k => $v) {
            $prod = $repoPro->findOneBy(['id' => $v['id'], 'productVariantId' => $v['productVariantId']]);
            if (!is_null($prod->productSheetPrototypeId)) {
                $resProds[$k]['code'] = $prod->id . "-" . $prod->productVariantId;
                $resProds[$k]['variant'] = $prod->productVariant->name;
            }
        }
        return json_encode($resProds);
    }

    public function post()
    {
        $get = $this->app->router->request()->getRequestData();
        $choosen = $this->getEntityByCode($get['choosen']);

        if (!$choosen) return "Attenzione! Il codice inserito non è un codice valido";

        $choosenPsa = $this->app->repoFactory->create('ProductSheetActual')->findBy(
            [
                'productId' => $choosen->id, 'productVariantId' => $choosen->productVariantId
            ]
        );
        try {
            foreach ($get['rows'] as $v) {

                $prod = $this->app->repoFactory->create('Product')->findOneBy(
                    [
                        'id' => $v['id'],
                        'productVariantId' => $v['productVariantId']
                    ]
                );
                $prod->productSheetPrototypeId = $choosen->productSheetPrototypeId;
                $prod->update();

                $psa = $this->app->repoFactory->create('ProductSheetActual')->findBy(
                    [
                        'productId' => $prod->id, 'productVariantId' => $prod->productVariantId
                    ]
                );

                foreach ($psa as $psaSingle) {
                    $psaSingle->delete();
                }

                foreach ($choosenPsa as $cpsaSingle) {
                    $newPsa = $this->app->repoFactory->create('ProductSheetActual')->getEmptyEntity();
                    $newPsa->productId = $prod->id;
                    $newPsa->productVariantId = $prod->productVariantId;
                    $newPsa->productDetailLabelId = $cpsaSingle->productDetailLabelId;
                    $newPsa->productDetailId = $cpsaSingle->productDetailId;
                    $newPsa->insert();
                }

            }
        } catch (\Exception $e) {
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
        $ent = $this->app->repoFactory->create('Product');
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