<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\domain\repositories\CFoisonRepo;


/**
 * Class CFoisonListAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 15/03/2018
 * @since 1.0
 */
class CFoisonListAjaxController extends AAjaxController
{
    /**
     * @return string
     * @throws \Throwable
     */
    public function get()
    {
        $sql = "
            SELECT F.id,
                  F.name,
                  F.surname,
                  F.email,
                  F.iban
            FROM Foison F
        ";

        $datatable = new CDataTables($sql, ['id'], $_GET, true);

        $datatable->doAllTheThings(true);

        /** @var CFoisonRepo $foisonRepo */
        $foisonRepo = \Monkey::app()->repoFactory->create('Foison');

        //$blueseal = $this->app->baseUrl(false).'/blueseal/';
        //$url = $blueseal."prodotti/gruppo-taglie/aggiungi";

        foreach ($datatable->getResponseSetData() as $key=>$row) {

            $foison = $foisonRepo->findOneBy(['id'=>$row["id"]]);

            $datatable->setResponseDataSetRow($key,$row);
        }


        return $datatable->responseOut();

    }

}