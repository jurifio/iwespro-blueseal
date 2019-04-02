<?php

namespace bamboo\controllers\back\ajax;
use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\domain\entities\CProductHasTag;
use bamboo\domain\entities\CTag;
use bamboo\domain\repositories\CProductBrandRepo;

/**
 * Class CSpecialNewBrandTagsAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 19/06/2018
 * @since 1.0
 */
class CSpecialNewBrandTagsAjaxController extends AAjaxController
{

    public function get(){

        $tagId = \Monkey::app()->router->request()->getRequestData('tagId');

        $sql = 'SELECT p.productBrandId
                FROM ProductHasTag pht
                JOIN Product p ON pht.productId = p.id AND pht.productVariantId = p.productVariantId
                WHERE pht.tagId = ?
                GROUP BY p.productBrandId';

        $brandIds = \Monkey::app()->dbAdapter->query($sql, [$tagId])->fetchAll();

        /** @var CProductBrandRepo $productBrandRepo */
        $productBrandRepo = \Monkey::app()->repoFactory->create('ProductBrand');

        $brands = [];
        foreach ($brandIds as $brandId){
            $brands[] = $productBrandRepo->findOneBy(['id' => $brandId['productBrandId']])->name;
        }


        return json_encode($brands);
    }

    /**
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    public function post(){
        $brand = \Monkey::app()->router->request()->getRequestData('brand');
        $tag = \Monkey::app()->router->request()->getRequestData('tag');
        $position = \Monkey::app()->router->request()->getRequestData('pos');

        $products = \Monkey::app()->dbAdapter->query('SELECT id, productVariantId FROM Product WHERE productBrandId = ?', [$brand])->fetchAll();

        if(!empty($products)) {
            $string = '';

            for ($i = 0; $i < count($products); $i++){
                if($i != count($products) -1){
                    $string .= '(' . $products[$i]['id'] . ',' . $products[$i]['productVariantId'] . ',' . $tag . ',' . $position . '),';
                } else {
                    $string .= '(' . $products[$i]['id'] . ',' . $products[$i]['productVariantId'] . ',' . $tag . ',' . $position . ')';
                }
            }

            $sql = '
            INSERT INTO ProductHasTag (productId, productVariantId, tagId, position)
              VALUES ' . $string . ' 
              ON DUPLICATE KEY UPDATE position = ' . $position;

            \Monkey::app()->dbAdapter->query($sql, []);
            $res = 'Special tag inserito con successo';
        } else $res = 'Nessun prodotto associato al brand selezionato';

        return $res;
    }

    public function delete(){

        $brand = \Monkey::app()->router->request()->getRequestData('brand');
        $tag = \Monkey::app()->router->request()->getRequestData('tag');

        $products = \Monkey::app()->dbAdapter->query('SELECT id, productVariantId FROM Product WHERE productBrandId = ?', [$brand])->fetchAll();


        if(!empty($products)) {

            $string = '(';

            for ($i = 0; $i < count($products); $i++){
                if($i != count($products) -1){
                    $string .= '(' . $products[$i]['id'] . ',' . $products[$i]['productVariantId'] . '),';
                } else {
                    $string .= '(' . $products[$i]['id'] . ',' . $products[$i]['productVariantId'] . '))';
                }
            }

            $sql = '
               DELETE FROM ProductHasTag
               WHERE tagId = '. $tag .' AND (productId, productVariantId) IN ' . $string;

            \Monkey::app()->dbAdapter->query($sql, []);
            $res = 'Special tag eliminato con successo';
        } else $res = 'Nessun prodotto associato al brand selezionato';

        return $res;


    }

}