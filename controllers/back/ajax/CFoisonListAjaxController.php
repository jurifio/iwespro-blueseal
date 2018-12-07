<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\domain\entities\CFoison;
use bamboo\domain\entities\CFoisonHasInterest;
use bamboo\domain\entities\CUser;
use bamboo\domain\entities\CUserAddress;
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

        $perm = \Monkey::app()->getUser()->hasPermission('allShops');

        $sql = "
            SELECT F.id,
                  F.name,
                  F.surname,
                  F.email,
                  B.iban,
                  wc.interestName
            FROM Foison F
            LEFT JOIN AddressBook B ON F.foisonAddressBookId = B.id
            LEFT JOIN FoisonHasInterest fhi ON F.id = fhi.foisonId
            LEFT JOIN WorkCategory wc ON fhi.workCategoryId = wc.id
        ";



        $datatable = new CDataTables($sql, ['id'], $_GET, true);
        if(!$perm) {
            /** @var CUser $user */
            $user = \Monkey::app()->getUser();
            /** @var CFoison $foisonUs */
            $foisonUs = $user->foison;
            $datatable->addCondition('id',[$foisonUs->id]);
        }

        $datatable->doAllTheThings(false);

        /** @var CFoisonRepo $foisonRepo */
        $foisonRepo = \Monkey::app()->repoFactory->create('Foison');

        $blueseal = $this->app->baseUrl(false).'/blueseal/';
        $url = $blueseal."work/foison/";

        foreach ($datatable->getResponseSetData() as $key=>$row) {

            /** @var CFoison $foison */
            $foison = $foisonRepo->findOneBy(['id'=>$row["id"]]);
            $row["DT_RowId"] = 'row__' . $foison->user->printId();
            $row["Row_foison_id"] = $foison->id;
            $row["id"] = '<a href=" '.$url.$foison->id.' " target="_blank"> '.$foison->id.' </a>';
            $row["name"] = $foison->name;
            $row["surname"] = $foison->surname;
            $row["email"] = $foison->email;
            $row["iban"] = (empty($foison->addressBook->iban) ? '-' : $foison->addressBook->iban);
            $row["totalRank"] = $foison->totalRank();
            $workCategories = "";

            /** @var CFoisonHasInterest $interests */
            $interests = $foison->foisonHasInterest;

            $allPB = $foison->getClosedTimeRanchProductBatch();

            /** @var CFoisonHasInterest $interest */
            foreach ($interests as $interest) {
                $allPbForCat = $allPB->findByKey("workCategoryId",$interest->workCategoryId);

                $r = !empty($allPbForCat) ?  "<strong>" . $foison->totalRank(false, $allPbForCat) . "</strong><br><br>" : '<strong>'."0".'</strong>'.'<br><br>';

                $workCategories .= $interest->workCategory->name . ":<br> 
                                Stato: " . "<strong>" . $interest->foisonStatus->name . "</strong><br> 
                                Rank: " . $r;
            }


            $row["interestName"] = $workCategories;

            $userAddress = $foison->user->userAddress;

            $incomplete = false;
            if($userAddress->isEmpty()) {
                $incomplete = true;
            } else {
                /** @var CUserAddress $usAddr */
                $usAddr = $userAddress->getFirst();

                if(
                    empty($usAddr->address) ||
                    empty($usAddr->province) ||
                    empty($usAddr->city) ||
                    empty($usAddr->postcode) ||
                    empty($usAddr->countryId) ||
                    empty($usAddr->phone) ||
                    empty($usAddr->fiscalCode) ||
                    empty($foison->addressBook->province) ||
                    empty($foison->addressBook->countryId) ||
                    empty($foison->addressBook->iban) ||
                    empty($foison->addressBook->phone)
                ) {
                    $incomplete = true;
                }
            }
            $row["statusProfile"] = $incomplete ? '<strong><p style="color: red">PROFILO INCOMPLETO</p></strong>' : '<strong><p style="color: green">PROFILO COMPLETO</p> </strong>';
            $datatable->setResponseDataSetRow($key,$row);
        }


        return $datatable->responseOut();

    }

}