<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\domain\entities\CMessage;
use bamboo\domain\entities\CMessageHasUser;
use bamboo\domain\entities\CUser;
use bamboo\ecommerce\views\VBase;

/**
 * Class CMessageDetailListController
 * @package bamboo\blueseal\controllers
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 31/05/2018
 * @since 1.0
 */
class CMessageDetailListController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "message_detail";

    /**
     * @return string
     * @throws \bamboo\core\exceptions\RedPandaInvalidArgumentException
     */
    public function get()
    {

        $id = \Monkey::app()->router->getMatchedRoute()->getComputedFilter('id');

        /** @var CUser $user */
        $user = \Monkey::app()->getUser();

        $isWorker = $user->hasPermission('worker');
        $isSuperAdmin = $user->hasPermission('allShops');


        if($isWorker && !$isSuperAdmin) {
            /** @var CRepo $mHuRepo */
            $mHuRepo = \Monkey::app()->repoFactory->create('MessageHasUser');

            /** @var CMessageHasUser $mHu */
            $mHu = $mHuRepo->getEmptyEntity();
            $mHu->foisonId = $user->foison->id;
            $mHu->messageId = $id;
            $mHu->smartInsert();
        }

        /** @var CMessage $message */
        $message = \Monkey::app()->repoFactory->create('Message')->findOneBy(['id'=>$id]);

        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/message_detail.php');

        $pr = '';
        switch ($message->priority){
            case 'L':
                $pr = 'BASSA';
                break;
            case 'M':
                $pr = 'MEDIA';
                break;
            case 'H':
                $pr = 'ALTA';
                break;
        }

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'page'=>$this->page,
            'sidebar' => $this->sidebar->build(),
            'title' => $message->title,
            'message' => $message->text,
            'priority' => $pr
        ]);
    }
}