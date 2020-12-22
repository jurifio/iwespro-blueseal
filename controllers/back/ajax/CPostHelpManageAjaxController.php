<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\base\CObjectCollection;
use bamboo\core\exceptions\BambooException;
use bamboo\core\exceptions\BambooOrderLineException;
use bamboo\core\exceptions\BambooShipmentException;
use bamboo\domain\entities\COrderLine;
use bamboo\domain\repositories\COrderLineRepo;
use bamboo\domain\repositories\CShipmentRepo;
use bamboo\utils\time\STimeToolbox;
use DateTime;
use PDO;
use PDOException;

/**
 * Class CPostHelpManageAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 31/01/2020
 * @since 1.0
 */
class CPostHelpManageAjaxController extends AAjaxController
{
    public function get()
    {
        $post = [];
        $blogId = $this->app->router->request()->getRequestData('blogId');
        $res = $this->app->dbAdapter->query('SELECT `postId`,`title`,`subtitle`
from PostTranslation where blogId=3 and langId=1',[])->fetchAll();

        foreach ($res as $result) {

            $post[] = ['id' => $result['postId'],
                'title' => $result['title'],
                'subtitle' => $result['subtitle']
            ];
        }

        return json_encode($post);
    }
    public function post()
    {
        $postId = $this->app->router->request()->getRequestData('postId');
        $pagesId = $this->app->router->request()->getRequestData('appsId');
        $pageRepo=\Monkey::app()->repoFactory->create('Page');
        foreach($pagesId as $pageId){
            $page=$pageRepo->findOneBy(['id'=>$pageId]);
            $postUrl=$page->url;
            $page->postId=$postId;
            $page->update();
            return 'il post è stato associato all \'applicazione '.$postUrl;
        }
    }
}