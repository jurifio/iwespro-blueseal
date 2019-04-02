<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\base\CObjectCollection;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\domain\repositories\CProductRepo;
use bamboo\ecommerce\views\widget\VBase;

/**
 * Class CProducTagMassive
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 29/04/2016
 * @since 1.0
 */
class CProductTagMassive extends AAjaxController
{
    protected $urls = [];
    protected $authorizedShops = [];
    protected $em;


    /**
     * @return string
     * @throws \bamboo\core\exceptions\BambooThemeException
     * @throws \bamboo\core\exceptions\RedPandaInvalidArgumentException
     */
    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath() . $this->app->cfg()->fetch('paths', 'blueseal') . '/template/widgets/productMassiveTagSelection.php');

        $tags = \Monkey::app()->repoFactory->create('Tag')->findAll('Tag');

        $seasons = \Monkey::app()->repoFactory->create('ProductSeason')->findBy(['isActive' => 1]);
        $brands = \Monkey::app()->repoFactory->create('ProductBrand')->findAll();
        $colors = \Monkey::app()->repoFactory->create('ProductColorGroup')->findAll();

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'seasons' => $seasons,
            'brands' => $brands,
            'colors' => $colors,
            'allTags' => $tags
        ]);
    }

    /**
     *
     */
    public function post()
    {
        $season = \Monkey::app()->router->request()->getRequestData('season');
        $brand = \Monkey::app()->router->request()->getRequestData('brand');
        $color = \Monkey::app()->router->request()->getRequestData('color');

        if(empty($season) && empty($brand) && empty($color)) return false;

        $cond = '';

        if(!empty($season)) $cond .= ' AND productSeasonId = ' . $season;
        if(!empty($brand)) $cond .= ' AND productBrandId = ' . $brand;
        if(!empty($color)) $cond .= ' AND productColorGroupId = ' . $color;

        $products = \Monkey::app()->dbAdapter->query('SELECT id, productVariantId FROM Product WHERE 1=1 ' . $cond, [])->fetchAll();

        $string = '';

        if (!empty($products)) {
            foreach ($products as $product) {

                if ($this->app->router->request()->getRequestData('tags')) {
                    foreach ($this->app->router->request()->getRequestData('tags') as $tag) {
                        $string .= '(' . $product['id'] . ',' . $product['productVariantId'] . ',' . $tag . ',null),';
                    }
                }

            }
            $sql = '
            INSERT IGNORE INTO ProductHasTag (productId, productVariantId, tagId, position)
              VALUES ' . substr($string, 0, -1);

            \Monkey::app()->dbAdapter->query($sql, []);
        }
        return true;
    }


    public function delete()
    {

        $season = \Monkey::app()->router->request()->getRequestData('season');
        $brand = \Monkey::app()->router->request()->getRequestData('brand');
        $color = \Monkey::app()->router->request()->getRequestData('color');

        if (empty($season) && empty($brand) && empty($color)) return false;

        $cond = '';

        if (!empty($season)) $cond .= ' AND p.productSeasonId = ' . $season;
        if (!empty($brand)) $cond .= ' AND p.productBrandId = ' . $brand;
        if (!empty($color)) $cond .= ' AND p.productColorGroupId = ' . $color;

        $tags = \Monkey::app()->router->request()->getRequestData('tags');
        $tagString = '(' . implode($tags, ',') . ')';

        $sql = 'SELECT pht.productId, pht.productVariantId, pht.tagId
                                                              FROM Product p
                                                              JOIN ProductHasTag pht ON p.id = pht.productId AND p.productVariantId = pht.productVariantId
                                                              WHERE 1=1 AND pht.tagId IN ' . $tagString . $cond;

        $products = \Monkey::app()->dbAdapter->query($sql, [])->fetchAll();

        $string = '(';

        if (!empty($products)) {
            foreach ($products as $product) {
                $string .= '(' . $product['productId'] . ',' . $product['productVariantId'] . ',' . $product['tagId'] . '),';
            }
        }

        $sql = '
               DELETE FROM ProductHasTag
               WHERE (productId, productVariantId, tagId) IN ' . substr($string, 0, -1) . ')';

        \Monkey::app()->dbAdapter->query($sql, []);

        return true;
    }
}