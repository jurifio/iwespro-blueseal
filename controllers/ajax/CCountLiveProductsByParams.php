<?php
namespace bamboo\blueseal\controllers\ajax;

/**
 * Class CCountLiveProductsByParams
 * @package bamboo\blueseal\controllers\ajax
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>, ${DATE}
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */
class CCountLiveProductsByParams extends AAjaxController
{
    /**
     * @return string
     */
    public function get()
    {
        $conditions = [];
        $queryParams = [];
        foreach ($this->app->router->request()->getRequestData() as $key => $param) {
            try {
                $param = json_decode($param);
            } catch (\Exception $e) {
                $param = [$param];
            }
            if (!is_array($param)) {
                $param = [$param];
            }

            if ($key == 'category') {
                $ins = [];
                foreach ($param as $single) {
                    $cats = $this->app->categoryManager->categories()->getDescendantsByNodeId($single);
                    foreach ($cats as $cat) {
                        $ins[] = $cat['id'];
                    }
                }
                $param = $ins;

            }
            if (!empty($param)) {
                $interrogation = [];
                for ($i = 0; $i < count($param); $i++) $interrogation[] = '?';
                $conditions[] = ' ' . $key . ' in (' . implode(',', $interrogation) . ') ';
                $queryParams = array_merge($queryParams, $param);
            }
        }
        $query = "SELECT COUNT(DISTINCT product, variant) AS conto
                  FROM vProductSortingView
                  WHERE 1=1 ";

        if (!empty($conditions)) {
            $query .= " AND " . implode(' AND ', $conditions);
        }

        return $this->app->dbAdapter->query($query, $queryParams)->fetchAll()[0]['conto'];
    }
}