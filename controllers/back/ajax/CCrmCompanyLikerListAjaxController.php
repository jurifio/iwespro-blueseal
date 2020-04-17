<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;
use bamboo\domain\entities\CBillRegistryProduct;
use bamboo\domain\entities\CBillRegistryGroupProduct;
use bamboo\domain\entities\CBillRegistryCategoryProduct;
use bamboo\utils\time\STimeToolbox;

/**
 * Class CCrmCompanyLikerListAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 16/04/2020
 * @since 1.0
 */
class CCrmCompanyLikerListAjaxController extends AAjaxController
{
    public function get()
    {
        $sql = "SELECT
                      `ccl`.`id`                                            AS `id`,
                       `ccl`.`companyName` as `companyName`,
                        CONCAT(`ccl`.`address`,'<br>',`ccl`.`postCode`,' ', `ccl`.`city`,'<br>', `ccl`.`countryName`) as address,
                        `ccl`.`phone` as `phone`,
                        `ccl`.`phone2` as `phone2`,
                        `ccl`.`email` as `email`,
                        `ccl`.`contactName` as `contactName`,
                        `ccl`.`rating` as `rating`,
                         group_concat(CONCAT( `csi`.`name`,' a ', `chs`.`description`)) as interest,
                        
                        GROUP_CONCAT(CONCAT('canale: ',`cs`.`name`, ' data: ', `ci`.dateTimeMessage, ' testo: ',`ci`.`description`,' priorità: ',`ci`.`priority`)) AS `message`
                    FROM `CrmCompanyLiker` `ccl`
                      left JOIN `CrmCompanyLikerHasInterest` `chs` on `ccl`.`id`=`chs`.`crmCompanyLikerId`
                       LEFT  outer join `CrmStatusInterest` `csi` on `chs`.`crmStatusInterestId`=`csi`.`id`
                        LEFT outer join `CrmMessage` `ci` on `chs`.`id`=`ci`.`crmCompanyLikeHasInterestId`
                         left OUTER   join `CrmSource` `cs` on `ci`.`crmSourceId`=`cs`.`id`
                                
                        
                      GROUP BY `ccl`.`companyName` Order BY ccl.id ASC";
        $datatable = new CDataTables($sql, ['id'], $_GET, true);

        $datatable->doAllTheThings();

        $crmCompanyLikerEdit = $this->app->baseUrl(false) . "/blueseal/crm/companyliker-modifica?id=";

        $crmCompanyLikerRepo = \Monkey::app()->repoFactory->create('CrmCompanyLiker');
        $crmCompanyLikeHasInterestRepo=\Monkey::app()->repoFactory->create('CrmCompanyLikerHasInterest');
        $crmMessageRepo=\Monkey::app()->repoFactory->create('CrmMessage');
        $crmSourceRepo=\Monkey::app()->repoFactory->create('CrmSource');
        $crmStatusInterestRepo=\Monkey::app()->repoFactory->create('CrmStatusInterest');
        foreach ($datatable->getResponseSetData() as $key => $row) {
            $crmCompanyLiker = $crmCompanyLikerRepo->findOne([$row['id']]);
            $row = [];
            $row["DT_RowId"] = 'row__' . $crmCompanyLiker->printId();
            $row['id'] = $crmCompanyLiker->id;
            $row['companyName']=$crmCompanyLiker->companyName;
            $row['address']=($crmCompanyLiker->address!=null)?$crmCompanyLiker->address.'<br>'.$crmCompanyLiker->postCode. ' '.$crmCompanyLiker->city.'<br>'.$crmCompanyLiker->countryName:'';
           $i=$crmCompanyLikeHasInterestRepo->findBy(['crmCompanyLikerId'=>$crmCompanyLiker->id]);
           $interest='';
           $message='';
           $priority='';
           foreach ($i as $interestTo){

               $statusInterest=$crmStatusInterestRepo->findOneBy(['id'=>$interestTo->crmStatusInterestId]);
               $interest.=$statusInterest->name.' a '.$interestTo->description.'<br>';


               $messagges=$crmMessageRepo->findBy(['crmCompanyLikeHasInterestId'=>$interestTo->id]);
               if($messagges!=null) {
                   foreach ($messagges as $m) {
                       $source = $crmSourceRepo->findOneBy(['id' => $m->crmSourceId]);
                       $dateMessage = STimeToolbox::DbFormattedDateTime($m->dateTimeMessage);
                       $message .= 'canale: ' . $source->name . '<br> data: ' . $dateMessage . '<br>testo: ' . $m->description . '<br>priorità: ' . $m->priority . '<br>';
                   }
               }


           }
            $row['phone']=$crmCompanyLiker->phone;
            $row['phone2']=$crmCompanyLiker->phone2;
            $row['email']=$crmCompanyLiker->email;
            $row['contactName']=$crmCompanyLiker->contactName;
            $row['rating']=$crmCompanyLiker->rating;
            $row['interest']=$interest;
            $row['message']=$message;

            $datatable->setResponseDataSetRow($key, $row);
        }

        return $datatable->responseOut();
    }
}