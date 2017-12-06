<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;

/**
 * Class CDescriptionTranslateLangListController
 * @package bamboo\blueseal\controllers
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date $date
 * @since 1.0
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

        $marketplaces = \Monkey::app()->repoFactory->create('Marketplace')->findAll();
        $brands = \Monkey::app()->repoFactory->create('ProductBrand')->findAll();
        $langs = \Monkey::app()->repoFactory->create('Lang')->findAll();
        $seasons = \Monkey::app()->repoFactory->create('ProductSeason')->findAll();
        $colors = \Monkey::app()->repoFactory->create('ProductColorGroup')->findAll();

        return $view->render([
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