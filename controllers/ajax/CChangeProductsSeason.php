<?php
namespace bamboo\blueseal\controllers\ajax;
use bamboo\core\base\CObjectCollection;

/**
 * Class CCheckProductsToBePublished
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
class CChangeProductsSeason extends AAjaxController
{

    public function post()
    {
        $get = $this->app->router->request()->getRequestData();
        $act = $get['action'];
        if (array_key_exists('rows', $get)) $rows = $get['rows'];
        switch ($act) {
            case "listStatus":
                $res = $this->app->dbAdapter->select('ProductStatus', [])->fetchAll();
                return json_encode($res);
            case "updateProductStatus":
                if ($get['productStatusId']) {
                    $count = 0;
                    $this->app->dbAdapter->beginTransaction();
                    try {
                        foreach ($rows as $k => $v) {
                            $product = $this->app->repoFactory->create('Product')->findOneBy(
                                [
                                    'id' => $v['id'],
                                    'productVariantId' => $v['productVariantId']
                                ]);
                            $product->productStatusId = $get['productStatusId'];
                            $count += $product->update();
                        }
                        $this->app->dbAdapter->commit();
                    } catch (\Exception $e) {
                        return "Errore nell'aggiornamento dello stato dei prodotti:<br />" .
                            $e->getMessage();
                            "Contattare l'amministratore<br />";
                    }
                return "Aggiornato lo stato di " . $count . " prodotti";
                }
            break;
        }
    }

    public function get()
    {   /** @var CObjectCollection $seasons */
        $seasons = $this->app->repoFactory->create('productSeason')->findAll();

        $expSeasons = [];
        $i = 0;
        foreach($seasons as $s) {
            $expSeasons[$i]['id'] = $s->id;
            $expSeasons[$i]['name'] = $s->name . " " . $s->year;
            $expSeasons[$i]['isActive'] = $s->isActive;
            $i++;
        };

        return json_encode($expSeasons);
    }

    public function delete()
    {
        $this->get();
    }
}