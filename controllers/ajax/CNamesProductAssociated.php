<?php
namespace bamboo\blueseal\controllers\ajax;
use bamboo\core\utils\slugify\CSlugify;

/**
 * Class CDetailManager
 * @package bamboo\blueseal\controllers\ajax
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
class CNamesProductAssociated extends AAjaxController
{

    /**
     * @return string
     */
    public function get()
    {
       $search = $this->app->router->request()->getRequestData()['search'];

        $res = $this->app->dbAdapter->query(
            "SELECT `p`.`id`, `p`.`productVariantId`, concat(`p`.`id`, '-', `p`.`productVariantId`) as `code`, `pb`.`name` as brand, concat(`pse`.`name`, ' ', `pse`.`year`) as season, `p`.`dummyPicture` as `pic` FROM
                (((ProductNameTranslation as `pn` JOIN Product as `p` ON `p`.`productVariantId` = `pn`.`productVariantId`)
                JOIN `ProductStatus` as `ps` ON `p`.`productStatusId` = `ps`.`id`)
                JOIN `ProductBrand` as `pb` on `p`.`productBrandId` = `pb`.`id`)
                JOIN `ProductSeason` as `pse` on `p`.`productSeasonId` = `pse`.`id`
                WHERE `langId` = 1 AND `pn`.`name` = ? AND `ps`.`code` in ('P')",
            [$search]
        )->fetchAll();

        $modifica = $this->app->baseUrl(false)."/blueseal/prodotti/modifica";

        $urlDummy = $this->app->cfg()->fetch('paths','dummyUrl');
        if ($this->app->getUser()->hasPermission('/admin/product/edit')) {
            foreach ($res as $k => $v) {
                $res[$k]['link'] = '<a href="' . $modifica . '?id=' . $v['id'] . '&productVariantId=' . $v['productVariantId'] . '">' . $v['id'] . '-' . $v['productVariantId'] . '</a>';
                $res[$k]['pic'] = strpos($v['pic'],'s3-eu-west-1.amazonaws.com') ? $v['pic'] : $urlDummy . "/" . $v['pic'];
            }
        }
        return json_encode($res);
    }
}