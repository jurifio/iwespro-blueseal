<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;


/**
 * Class CProductDetailListAjaxController
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
class CNewsletterEmailListAjaxController extends AAjaxController
{
    public function get()
    {
        $sql = "select u.id, u.name as newsletterSegmentName, G.name as newsletterGroupName, u.sql, u.code  from NewsletterEmailList u inner JOIN NewsletterGroup G ON u.newsletterGroupId = G.id";
        $datatable = new CDataTables($sql, ['id'], $_GET, true);

        $datatable->doAllTheThings('true');

        foreach ($datatable->getResponseSetData() as $key=>$row) {

        }

        return $datatable->responseOut();
    }
}