<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;
/**
 * Class CDescriptionTranslateLangListController
 * @package bamboo\app\controllers
 */
class CDescriptionTranslateLangListController extends CDescriptionTranslateLangManageController
{
    /**
     * @var string
     */
    protected $fallBack = "blueseal";
    /**
     * @var string
     */
    protected $pageSlug = "description_translate_lang";

    /**
     * @throws \bamboo\core\exceptions\RedPandaORMException
     */
    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/description_translate_lang.php');

        $marketplaces = $this->app->repoFactory->create('Marketplace')->findAll();
        $brands = $this->app->repoFactory->create('ProductBrand')->findAll();
        $langs = $this->app->repoFactory->create('Lang')->findAll();
        $seasons = $this->app->repoFactory->create('ProductSeason')->findAll();
        $colors = $this->app->repoFactory->create('ProductColorGroup')->findAll();

        echo $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'marketplaces' => $marketplaces,
            'brands' => $brands,
            'langs' => $langs,
            'seasons' => $seasons,
            'colors' => $colors,
            'page'=>$this->page,
            'sidebar'=>$this->sidebar->build()
        ]);
    }
}