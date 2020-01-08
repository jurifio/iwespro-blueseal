<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\base\CObjectCollection;
use bamboo\core\exceptions\BambooException;
use bamboo\core\exceptions\BambooOrderLineException;
use bamboo\core\exceptions\BambooShipmentException;
use bamboo\domain\entities\COrderLine;
use bamboo\domain\repositories\COrderLineRepo;
use bamboo\domain\repositories\CShipmentRepo;
use bamboo\utils\time\STimeToolbox;
use DateTime;
use PDO;
use PDOException;
use bamboo\domain\entities\CProduct;

/**
 * Class CSelectProductCategoryAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 08/01/2020
 * @since 1.0
 */

class CSelectProductCategoryAjaxController extends AAjaxController
{
    public function get()
    {
        $collectCategory = [];

        $sqlCategory='SELECT t0.id as  id,t0.slug as slug
      ,(SELECT GROUP_CONCAT(t2.slug)
                    FROM ProductCategory t2
                    WHERE t2.lft<t0.lft AND t2.rght>t0.rght
                    ORDER BY t2.lft) ancestors
FROM ProductCategory  t0 GROUP BY t0.slug';
        $res_category = \Monkey::app()->dbAdapter->query($sqlCategory, [])->fetchAll();

            foreach ($res_category as $category) {
                $categoryName=str_replace(',','/',($category['ancestors'].','.$category['slug']));
                array_push($collectCategory,['id'=>$category['id'],'name'=>$categoryName]);
            }


        return json_encode($collectCategory);
    }
}