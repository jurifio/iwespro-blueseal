<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\domain\entities\CFoison;
use bamboo\domain\entities\CFoisonSubscribeRequest;
use bamboo\domain\entities\CUser;
use bamboo\domain\entities\CWorkCategory;
use bamboo\domain\repositories\CFoisonRepo;
use bamboo\utils\time\SDateToolbox;
use bamboo\utils\time\STimeToolbox;


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
class CFoisonSubRequestListAjaxController extends AAjaxController
{
    /**
     * @return string
     * @throws \Throwable
     */
    public function get()
    {
        $sql = "
            SELECT fsr.id,                
                   concat(fsr.name, ' ', fsr.surname) as fName,
                   fsr.nickName,
                   fsr.address,
                   fsr.birthday,
                   fsr.phone,
                   fsr.email,
                   fsr.actualWorkPosition,
                   fsr.language,
                   group_concat(wc.interestName) as foisonInterest
                  
            FROM FoisonSubscribeRequest fsr
            JOIN FoisonSubscribeRequestHasWorkCategory fsrhwk ON fsr.id = fsrhwk.foisonSubscribeRequestId
            JOIN WorkCategory wc ON fsrhwk.workCategoryId = wc.id
        ";



        $datatable = new CDataTables($sql, ['id'], $_GET, true);

        /** @var CRepo $foisonSubscribeRequest */
        $foisonSubscribeRequest = \Monkey::app()->repoFactory->create('FoisonSubscribeRequest');
        $datatable->doAllTheThings(false);

        foreach ($datatable->getResponseSetData() as $key=>$row) {

            /** @var CFoisonSubscribeRequest $req */
            $req = $foisonSubscribeRequest->findOneBy(["id"=>$row["id"]]);

            if($req->status === "denied"){
                $row["DT_RowClass"] = "red";
            } else if ($req->status === "accepted"){
                $row["DT_RowClass"] = "green";
            }

            $row["id"] = $req->id;
            $row["fName"] = $req->name . ' ' . $req->surname;
            $row["address"] = $req->address;
            $row["birthday"] = STimeToolbox::GetDateTime($req->birthday)->format("Y-m-d");
            $row["phone"] = $req->phone;
            $row["email"] = $req->email;
            $row["actualWorkPosition"] = $req->actualWorkPosition;
            $row["language"] = $req->language;

            $category = $req->workCategory;

            $int = "";

            /** @var CWorkCategory $val */
            foreach ($category as $val) {
                $int .= $val->interestName . "<br>";
            }

            $row["foisonInterest"] = $int;


            $datatable->setResponseDataSetRow($key,$row);
        }


        return $datatable->responseOut();

    }

}