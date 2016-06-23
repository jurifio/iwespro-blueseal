<?php
namespace bamboo\blueseal\controllers\ajax;

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
class CProductMerge extends AAjaxController
{
    public function get()
    {
        $get = $this->app->router->request()->getRequestData();
        $prods = $get['rows'];

        //controllo size group e se ci sono ordini relativi ai prodotti da unire
        $repoPro = $this->app->repoFactory->create('Product');
        $repoOrd = $this->app->repoFactory->create('OrderLine');
        $sizeGroupCompatibility = true;
        $sizeGroup = 0;

        foreach($prods as $k => $v) {
            $prod = $repoPro->findOneBy(['id' => $v['id'], 'productVariantId' => $v['productVariantId']]);
            if (0 == $sizeGroup) {
                $sizeGroup = $prod->productSizeGroupId;
            } else {
                if ($prod->productSizeGroupId !== $sizeGroup) {
                    $sizeGroupCompatibility = false;
                }
            };
            $prods[$k]['areOrders'] = ($repoOrd->findBy(['productId' => $v['id'], 'productVariantId' => $v['productVariantId']])->count()) ? 1 : 0 ;
        }

        $res = [
            'sizeGroupCompatibility' => $sizeGroupCompatibility,
            'rows' => $prods
        ];
        return json_encode($res);
    }

    public function post() {
        $get = $this->app->router->request()->getRequestData();
        $action = '';
        if (array_key_exists('action', $get)) $action = $get['action'];

        switch ($action){
            case 'merge': {
                $res = $this->mergeProducts($get['rows'], $get['choosen']);
                break;
            }
        }
        return $res;
    }

    private function mergeProducts($rows, $choosen) {
        // Controll if there is
    }
}