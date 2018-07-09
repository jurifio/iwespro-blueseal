<?php
namespace bamboo\controllers\back\ajax;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;

/**
 * Class CCampaignListWidget
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 06/07/2018
 * @since 1.0
 */
class CCampaignListWidget extends AAjaxController
{
    public function get() {

        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/widgets/newsletter_campaignlist.php');

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app)
        ]);
    }
}