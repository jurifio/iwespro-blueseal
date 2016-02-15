<?php
namespace bamboo\controllers\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\db\pandaorm\adapter\CMySQLAdapter;
use bamboo\core\db\pandaorm\entities\CEntityManager;
use bamboo\core\intl\CLang;

/**
 * Class CProductActiveListAjaxController.php
 * @package bamboo\app\controllers
 */
class CProductActiveListAjaxController extends AAjaxController
{
    protected $urls = [];
    protected $authorizedShops = [];
    protected $em;

    /**
     * @param $action
     * @return mixed
     */
    public function createAction($action)
    {
        $this->app->setLang(new CLang(1, 'it'));
        $this->urls['base'] = $this->app->baseUrl(false) . "/blueseal/";
        $this->urls['page'] = $this->urls['base'] . "prodotti";
        $this->urls['dummy'] = $this->app->cfg()->fetch('paths', 'dummyUrl');

        if ($this->app->getUser()->hasRole('ownerEmployee')) {

        } else if ($this->app->getUser()->hasRole('friendEmployee')) {
            $res = $this->app->dbAdapter->select('UserHasShop', ['userId' => $this->app->getUser()->getId()])->fetchAll();
            foreach ($res as $val) {
                $this->authorizedShops[] = $val['shopId'];
            }
        }

        $this->em = new \stdClass();
        $this->em->products = $this->app->entityManagerFactory->create('Product');

        return $this->{$action}();
    }

    public function get()
    {

        /** @var $mysql CMySQLAdapter * */
        /** @var $em CEntityManager * */

        $bluesealBase = $this->app->baseUrl(false) . "/blueseal/";
        $dummyUrl = $this->app->cfg()->fetch('paths', 'dummyUrl');

        $datatable = new CDataTables('vBluesealProductActive', ['id', 'productVariantId'], $_GET);
        if (!empty($this->authorizedShops)) {
            $datatable->addCondition('shopId', $this->authorizedShops);
        }

        $prodotti = $this->app->repoFactory->create('Product')->em()->findBySql($datatable->getQuery(), $datatable->getParams());
        $count = $this->em->products->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totlalCount = $this->em->products->findCountBySql($datatable->getQuery('full'), $datatable->getParams());

        $em = $this->app->entityManagerFactory->create('ProductStatus');
        $productStatuses = $em->findAll('limit 99', '');

        $statuses = [];
        foreach ($productStatuses as $status) {
            $statuses[$status->code] = $status->name;
        }

        $modifica = $bluesealBase . "prodotti/modifica";

        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totlalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];

        $i = 0;
        foreach ($prodotti as $val) {

            if (isset($statuses[$val->status])) {
                $statusName = $statuses[$val->status];
            } else {
                $statusName = 'Sconosciuto';
            }

            $shops = [];
            foreach ($val->shop as $shop) {
                $shops[] = $shop->name;
            }

            $img = strpos($val->dummyPicture, 's3-eu-west-1.amazonaws.com') ? $val->dummyPicture : $dummyUrl . "/" . $val->dummyPicture;

            $response['aaData'][$i]["code"] = $val->id . '-' . $val->productVariantId;
            $response['aaData'][$i]["brand"] = isset($val->productBrand) ? $val->productBrand->name : "";
            $response['aaData'][$i]["dummyPicture"] = isset($val->dummyPicture) && !empty($val->dummyPicture) ? '<img width="80" src="' . $img . '">' : "";
            $response['aaData'][$i]["status"] = $statusName;

            $th = "";
            $tr = "";
            $res = $this->app->dbAdapter->query("SELECT s.name, sum(ps.stockQty) stock
                                          FROM ProductSku ps , ProductSize s
                                          WHERE ps.productSizeId = s.id AND
                                              ps.productId = ? AND
                                              ps.productVariantId = ?
                                          GROUP BY ps.productSizeId
                                          HAVING stock > 0 ORDER BY `name`", [$val->id, $val->productVariantId])->fetchAll();
            foreach ($res as $sums) {
                $th .= "<th>" . $sums['name'] . "</th>";
                $tr .= "<td>" . $sums['stock'] . "</td>";
            }
            $response['aaData'][$i]["skus"] = '<table class="nested-table"><thead><tr>'.$th . "</tr></thead><tbody>" . $tr . "</tbody></table>";

            $res = $this->app->dbAdapter->query("SELECT max(ps.price) as price,  group_concat(distinct s.title SEPARATOR ',') as shops
                                          FROM ProductSku ps, Shop s
                                          WHERE ps.shopId = s.id AND
                                              ps.productId = ? AND
                                              ps.productVariantId = ?
                                          GROUP BY ps.productId, ps.productVariantId", [$val->id, $val->productVariantId])->fetchAll();


            $response['aaData'][$i]["price"] = isset($res[0]) ? $res[0]['price'] : 0;
            $response['aaData'][$i]["shops"] = isset($res[0]) ? $res[0]['shops'] : 0;

            $res = $this->app->dbAdapter->query("SELECT sum(ol.activePrice) incasso, count(DISTINCT ol.id) conto
                                          FROM OrderLine ol, `Order` o
                                          WHERE o.status LIKE 'ORD%' AND
                                              o.id = ol.orderId AND
                                              ol.productId = ? AND
                                              ol.productVariantId = ?
                                          GROUP BY ol.productId, ol.productVariantId", [$val->id, $val->productVariantId])->fetchAll();

            $response['aaData'][$i]["income"] = isset($res[0]) ? $res[0]['incasso'] : 0;
            $response['aaData'][$i]["sells"] = isset($res[0]) ? $res[0]['conto'] : 0;


            $i++;
        }

        return json_encode($response);
    }

    public function post()
    {
        throw new \Exception();
    }

    public function delete()
    {
        throw new \Exception();
    }
}