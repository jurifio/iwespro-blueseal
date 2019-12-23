<?php
/**
 * Created by PhpStorm.
 * User: jurif
 * Date: 08/01/2018
 * Time: 16:55
 */

namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\domain\entities\CNewsletterUser;

/**
 * Class CEmailTemplateListAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 23/12/2019
 * @since 1.0
 */

class CEmailTemplateListAjaxController extends AAjaxController
{

    public function get()
    {
        $sql = "SELECT n.id,
                n.name,
                n.oldTemplatephp,
                n.scope,
                n.description,
                n.subject,
                if(n.isActive=1,'si','no'),
                n.template,
                s.name as shopName
                FROM 
                        EmailTemplate n join Shop s on n.shopId =s.id";
        $datatable = new CDataTables($sql, ['id'], $_GET, true);

        $datatable->doAllTheThings(true);
        $blueseal = $this->app->baseUrl(false) . '/blueseal/';
        $opera = $blueseal . "email/email-template-modifica?id=";

        foreach ($datatable->getResponseSetData() as $key=>$row) {

            /** @var CRepo $emailTemplateRepo*/
            $emailTemplateRepo = \Monkey::app()->repoFactory->create('EmailTemplate');

            /** @var CEmailTemplate $emailTemplate */
            $emailTemplate = $emailTemplateRepo->findOneBy(['id' => $row['id']]);
            $row['id']=$emailTemplate->id;


            $row['id'] = '<a href="' . $opera . $emailTemplate->id . '">' . $emailTemplate->id . '</a>';

            $row['name'] = $emailTemplate->name;
        //var_dump($newsLetterTemplate);

            $datatable->setResponseDataSetRow($key,$row);
        }

        return $datatable->responseOut();
    }
}