<?php
namespace bamboo\controllers;

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;
use bamboo\core\io\CJsonAdapter;
use bamboo\core\intl\CLang;


/**
 * Class CConnectorController
 * @package bamboo\blueseal\controllers
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date $date
 * @since 1.0
 */
class CConnectorController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "connector_edit";

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->cfg()->fetch('paths','blueseal').'/template/connector_edit.php');

        $importerFieldModifier = $this->app->repoFactory->create('ImporterFieldModifier')->findAll();
        $importerField = $this->app->repoFactory->create('ImporterField')->findAll();
        $importerOperator = $this->app->repoFactory->create('ImporterOperator')->findAll();
        $importerLogicConnector = $this->app->repoFactory->create('ImporterLogicConnector')->findAll();

        $shopId = $this->app->router->request()->getRequestData('shopId');
        $shop = $this->app->repoFactory->create('Shop')->findOneBy(['id'=>$shopId]);
        $productSizeGroup = $this->app->repoFactory->create('ProductSizeGroup')->findAll('limit 99999','order by macroName');
        $impConnector = $this->app->repoFactory->create('ImporterConnector')->findBy(['shopId' => $shopId]);
        $value = "";

        foreach ($impConnector as $impConn) {
            $impConnectorStart = $this->app->repoFactory->create('ImporterConnectorStart')->findOneBy(['importerConnectorId' => $impConn->id]);
            $value = $impConnectorStart->value;
        }

        echo $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'importerFieldModifier' => $importerFieldModifier,
            'importerField' => $importerField,
            'importerOperator' => $importerOperator,
            'importerLogicConnector' => $importerLogicConnector,
            'productSizeGroup' => $productSizeGroup,
            'value' => $value,
            'page' => $this->page,
            'shop' => $shop,
            'sidebar' => $this->sidebar->build()
        ]);
    }

    public function post()
    {


    }
}