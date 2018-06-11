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
        $blueseal = $this->app->baseUrl(false) . '/blueseal/';
        $opera = $blueseal . "newsletter/newsletter-template-modifica?id=";

        foreach ($datatable->getResponseSetData() as $key=>$row) {

            /** @var CRepo $newsletterTemplateRepo*/
            $newsletterTemplateRepo = \Monkey::app()->repoFactory->create('NewsletterTemplate');

            /** @var CNewsletterTemplate $newsLetterTemplate */
            $newsletterTemplate = $newsletterTemplateRepo->findOneBy(['id' => $row['id']]);
            $row['id']=$newsletterTemplate->id;


            $row['id'] = '<a href="' . $opera . $newsletterTemplate->id . '">' . $newsletterTemplate->id . '</a>';

            $row['name'] = $newsletterTemplate->name;
        //var_dump($newsLetterTemplate);

            $datatable->setResponseDataSetRow($key,$row);
        }

        return $datatable->responseOut();
    }
}