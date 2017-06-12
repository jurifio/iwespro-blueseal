<?php
namespace bamboo\controllers\back\ajax;
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
class CProductProcessingUpdate extends AAjaxController
{
    /**
     * @return string
     */
    public function get()
    {
        try{
            $dba = \Monkey::app()->dbAdapter;

            $type = $dba->query("SHOW COLUMNS FROM `Product` WHERE Field like 'processing'", [])->fetchAll()[0]['Type'];
            preg_match('/enum\((.*)\)$/', $type, $matches);

            $arr = explode("','", $matches[1]);
            $arr[0] = str_replace("'", "", $arr[0]);
            $arr[count($arr) - 1] =  str_replace("'", "", $arr[count($arr) - 1]);
            return json_encode($arr);
        } catch(BambooException $e){
            \Monkey::app()->router->response()->raiseProcessingError();
            return $e->getMessage();
        }
    }

    /**
     * @return string
     */
    public function post() {
        try {
            $processing = \Monkey::app()->router->request()->getRequestData('processing');
            $rows = \Monkey::app()->router->request()->getRequestData('rows');

            $pR = \Monkey::app()->repoFactory->create('Product');
            foreach($rows as $r) {
                $p = $pR->findOne([$r['id'], $r['productVariantId']]);
                if ($p) {
                    $p->processing = $processing;
                    $p->update();
                }
            }
            $io = (1 < count($rows)) ? 'i' : 'o';
            return count($rows) . " prodott" . $io . " aggiornat" . $io;
        } catch(BambooException $e) {
            \Monkey::app()->router->response()->raiseProcessingError();
            return $e->getMessage();
        }
    }
}