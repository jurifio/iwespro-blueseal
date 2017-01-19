<?php
namespace bamboo\blueseal\controllers\ajax;

use bamboo\domain\entities\CProduct;
use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;
use bamboo\core\db\pandaorm\entities\CEntityManager;
use bamboo\core\db\pandaorm\adapter\CMySQLAdapter;

/**
 * Class CProductIncompleteListController.php
 * @package bamboo\app\controllers
 */
class CProductColorAjaxController extends AAjaxController
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

        if ($this->app->getUser()->hasPermission('allShops')) {

        } else {
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
        $pcg = $this->app->repoFactory->create('ProductColorGroup')->findBy([], '', 'order by name');

        $ret = '<div style="height: 250px" class="form-group form-group-default selectize-enabled"><select class="full-width selectpicker" id="size-group-select" data-init-plugin="selectize"><option value="">Seleziona un gruppo colore</option>';
        foreach($pcg as $v) {
            $ret .= '<option value="' . $v->id . '">' . $v->name . '</option>';
        }
        $ret .= '</select></div>';
        return $ret;
    }

    public function put() {
        $id = $this->app->router->request()->getRequestData();
        $variants = [];
        $groupId = null;

        $i = 0;
        foreach($id as $k => $v){
            if (false !== strpos($k, "row")) {
                list($id, $productVariantId) = explode("-", $v);
                $variants[$i]['id'] = $id;
                $variants[$i]['productVariantId'] = $productVariantId;
                $i++;
            } elseif (false !== strpos($k, "groupId")) {
                $groupId = $v;
            }
        }

        $affected = 0;
        try {
            foreach ($variants as $k => $v) {
                $product = $this->app->repoFactory->create('Product')->findOneBy($v);
                $product->productColorGroupId = $groupId;
                $affected += $product->update();
            }
        } catch(\Throwable $e) {
            $ret = "OOPS! Qualcosa è andato storto. $affected prodotti aggiornati. Contatta l'amministratore<br />";
            //$ret .= $sql . "<br />";
            //$ret.= $e->getMessage();
            return $ret;
        }
        return "$affected prodotti sono stati correttamente aggiornati.";
    }
}