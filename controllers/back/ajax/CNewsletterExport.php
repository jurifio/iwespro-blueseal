<?php
namespace bamboo\controllers\back\ajax;

/**
 * Class CBrandDelete
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Iwes  International Web Ecommerce ServicesTeam <juri@iwes.it>, ${DATE}
 *
 * @copyright (c) Iwes International Web Ecommerce Services - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */
class CNewsletterExport extends AAjaxController
{
    /**
     * @return string
     */
    public function get()
    {
        $newsletterUsers = $this->app->dbAdapter->select('NewsletterCustom')->fetchAll();

        $output = fopen("php://output",'w');
        foreach($newsletterUsers as $user) {
            fputcsv($output, $user);
        }
        fclose($output);
    }
}