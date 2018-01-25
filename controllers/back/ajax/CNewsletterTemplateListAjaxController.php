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

class CNewsletterTemplateListAjaxController extends AAjaxController
{

    public function get()
    {
        $sql = "SELECT n.id, n.name FROM 
                        NewsletterTemplate n";
        $datatable = new CDataTables($sql, ['id'], $_GET, true);

        $datatable->doAllTheThings(true);

        foreach ($datatable->getResponseSetData() as $key=>$row) {

            /** @var CRepo $newsletterTemplateRepo*/
            $newsletterTemplateRepo = \Monkey::app()->repoFactory->create('NewsletterTemplate');

            /** @var CNewsletterTemplate $newsLetterTemplate */
            $newsLetterTemplate = $newsletterTemplateRepo->findOneBy(['id' => $row['id']]);

            $row['name'] = $newsLetterTemplate->name;
        //var_dump($newsLetterTemplate);


        }

        return $datatable->responseOut();
    }
}