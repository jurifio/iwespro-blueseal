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
class CProductChangeProductSizeController extends AAjaxController
{
    protected $urls = [];
    protected $authorizedShops = [];
    protected $em;

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

    public function put()
    {
        $soR = \Monkey::app()->repoFactory->create('StorehouseOperation');
        $groupId = $this->app->router->request()->getRequestData('groupId');
        if(!$groupId){
            return "Errore: nessun gruppo taglie selezionato.";
        } elseif(empty($this->app->router->request()->getRequestData('products'))) {
            return "Nessun prodotto selezionato";
        } else {
            foreach($this->app->router->request()->getRequestData('products') as $productIds) {
                $product = $this->app->repoFactory->create('Product')->findOneByStringId($productIds);
                $product->productSizeGroupId = $groupId;
                $product->update();

                $soR->allSkusToZero($product, 16);

                \Monkey::app()->eventManager->triggerEvent('changeGroupSize',
                    [
                        'groupSizeId' => $groupId,
                        'product' => $product
                    ]);
            }

            return "Il gruppo taglie è stato assegnato alle righe selezionate.";
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