<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;
use bamboo\domain\entities\CReturn;
use bamboo\domain\repositories\CReturnRepo;


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
class CSizeFullListAjaxController extends AAjaxController
{
    /**
     * @return string
     * @throws \Throwable
     */
    public function get()
    {
        $sql = "SELECT *
                FROM ProductSize ps";

        $datatable = new CDataTables($sql, ['id'], $_GET, true);

        $datatable->doAllTheThings(true);

        ///** @var CReturnRepo $returnRepo */
       // $returnRepo = \Monkey::app()->repoFactory->create('Return');


        //$blueseal = $this->app->baseUrl(false) . '/blueseal/';
        //$opera = $blueseal . "resi/aggiungi?return=";


        return $datatable->responseOut();
    }
}