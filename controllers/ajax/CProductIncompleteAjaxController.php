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
class CProductIncompleteAjaxController extends AAjaxController
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
        $psg = $this->app->repoFactory->create('ProductSizeGroup')->findAll(null, 'order by locale, macroName, `name`');

        $ret = '<div style="height: 250px" class="form-group form-group-default selectize-enabled"><select class="full-width selectpicker" id="size-group-select" data-init-plugin="selectize"><option value="">Seleziona un gruppo taglie</option>';
        foreach($psg as $v) {
            $ret .= '<option value="' . $v->id . '">' . $v->locale . " " . $v->macroName . " " . $v->name . '</option>';
        }
            $ret .= '</select></div>';
        return $ret;
    }

    public function put() {
        $id = $this->app->router->request()->getRequestData();
        $whereAnds = [];
        $groupId = null;
        foreach($id as $k => $v){
            if (false !== strpos($k, "row")) {
                list($id, $productVariantId) = explode("-", $v);
                $whereAnds[] = "( id = " . $id . " AND productVariantId = " . $productVariantId . ")";
            } elseif (false !== strpos($k, "groupId")) {
                $groupId = $v;
            }

        }
        if(!$groupId){
            return "Errore: nessun gruppo taglie selezionato.";
        } else {
            $where = implode(" OR ", $whereAnds);
            $sql = "UPDATE Product SET productSizegroupId = " . $groupId . " WHERE " .  $where;
            try {
                $res = $this->app->dbAdapter->query($sql, [])->countAffectedRows();
            } catch(\PDOException $e) {
                return $e->getMessage();
            }
            return "Il gruppo colore è stato assegnato alle righe selezionate.";
        }
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