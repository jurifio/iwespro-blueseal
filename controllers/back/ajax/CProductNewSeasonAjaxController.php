<?php

namespace bamboo\controllers\back\ajax;
use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CProductHasTag;
use bamboo\domain\entities\CProductSeason;
use bamboo\domain\entities\CTag;


/**
 * Class CProductNewSeasonAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 15/06/2018
 * @since 1.0
 */
class CProductNewSeasonAjaxController extends AAjaxController
{

    public function get(){

        $tagId = \Monkey::app()->router->request()->getRequestData('tagId');

        $sql = 'SELECT p.productSeasonId
                FROM ProductHasTag pht
                JOIN Product p ON pht.productId = p.id AND pht.productVariantId = p.productVariantId
                WHERE pht.tagId = ?
                GROUP BY p.productSeasonId';

        $seasonIds = \Monkey::app()->dbAdapter->query($sql, [$tagId])->fetchAll();

        /** @var CRepo $seasonRepo */
        $seasonRepo = \Monkey::app()->repoFactory->create('ProductSeason');

        $seasons = [];
        foreach ($seasonIds as $seasonId){
            $seasons[] = $seasonRepo->findOneBy(['id' => $seasonId['productSeasonId']])->name;
        }


        return json_encode($seasons);
    }

    /**
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    public function post()
    {
        $seasonId = \Monkey::app()->router->request()->getRequestData('season');

        $products = \Monkey::app()->dbAdapter->query('SELECT id, productVariantId FROM Product WHERE productSeasonId = ?', [$seasonId])->fetchAll();
        $tag = \Monkey::app()->router->request()->getRequestData('tag');
        $position = \Monkey::app()->router->request()->getRequestData('pos');

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
            INSERT IGNORE INTO ProductHasTag (productId, productVariantId, tagId, position)
              VALUES ' . $string;

            \Monkey::app()->dbAdapter->query($sql, []);

            $res = 'Etichetta stagione inserita con successo';
        } else $res = 'Nessun prodotto associato alla stagione selezionata';

        return $res;
    }

    /**
     * @return string
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    public function delete(){

        $seasonId = \Monkey::app()->router->request()->getRequestData('season');
        $tag = \Monkey::app()->router->request()->getRequestData('tag');

        $products = \Monkey::app()->dbAdapter->query('SELECT id, productVariantId FROM Product WHERE productSeasonId = ?', [$seasonId])->fetchAll();


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

            $res = 'Etichetta stagione eliminata con successo';
        } else $res = 'Nessun prodotto associato alla stagione selezionata';

        return $res;

    }

}