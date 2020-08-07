<?php


namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\facebook\CFacebookCookieSession;
use bamboo\domain\entities\CEditorialPlan;
use bamboo\domain\entities\CEditorialPlanArgument;
use bamboo\domain\entities\CEditorialPlanDetail;
use \bamboo\utils\time\STimeToolbox;
use Facebook\Facebook;

/**
 * Class CEditorialPlanDetailPublishAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 31/07/2020
 * @since 1.0
 */
class CEditorialPlanDetailPublishAjaxController extends AAjaxController
{


    public function post()
    {
        if (ENV == 'dev') {
            require '/media/sf_sites/vendor/mailgun/vendor/autoload.php';
        } else {
            require '/home/shared/vendor/mailgun/vendor/autoload.php';
        }
        $this->app->vendorLibraries->load("facebook");
        $c = new CFacebookCookieSession($this->app);
        $fbConfig = $this->app->cfg()->fetch('miscellaneous','facebook');
        $fb = new Facebook([
            'app_id' => $fbConfig['app_id'], // Replace {app-id} with your app id
            'app_secret' => $fbConfig['app_secret'],
            'default_graph_version' => $fbConfig['default_graph_version'],
            'persistent_data_handler' => &$c
        ]);

        $res = '';
        $data = $this->app->router->request()->getRequestData();

        $editorialPlanDetailId = $data['editorialPlanDetailId'];
        /** @var CRepo $editorialPlanDetail */
        $editorialPlanDetail = \Monkey::app()->repoFactory->create('EditorialPlanDetail')->findOneBy(['id' => $editorialPlanDetailId]);
        /** @var CRepo $editorialPlan */
        $editorialPlan = \Monkey::app()->repoFactory->create('EditorialPlan')->findOneBy(['id' => $editorialPlanDetail->editorialPlanId]);
        $editorialPlanShopAsSocial = \Monkey::app()->repoFactory->create('EditorialPlanShopAsSocial')->findOneBy(['shopId' => $editorialPlan->shopId]);
        $pageAccessToken = 'EAALxfLD2ZAZCoBAGWoLZAfszPwLN4WnPehwiHyym7tZAOZAsZAVVHMQkT3ZCIsZAmkXK3hQZCKlvS66tjPyEVtCaDwQzUoZCyh5rusYHYt0oeunHzZAbwaBUwMRGhKet2BORvAiypkvu21XJWh7pkAZCGiKRXpN2EHgZBwmxHyKcsd7w1KQZDZD';

        $linkData = [
            'message' => $editorialPlan->name,
            'name' => $editorialPlanDetail->title,
            'link' => $editorialPlanDetail->linkDestination,
            'description' => $editorialPlanDetail->description,
            'picture' => $editorialPlanDetail->photoUrl
        ];
        try {
            $response = $fb->post('/me/feed',$linkData,$pageAccessToken);
        } catch (Facebook\Exceptions\FacebookResponseException $e) {
            return $res = 'Graph returned an error: ' . $e->getMessage();

        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            return $res = 'Facebook SDK returned an error: ' . $e->getMessage();

        }
        $graphNode = $response->getGraphNode();
        $id=$graphNode['id'];



        $res = "  Post pubblicato con successo";
        return $res;
    }
}