<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\base\CSerialNumber;
use bamboo\core\exceptions\BambooException;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;
use bamboo\utils\price\SPriceToolbox;
use bamboo\utils\time\STimeToolbox;
use PDO;
use PDOException;

/**
 * Class CBannerAddController
 * @package bamboo\blueseal\controllers
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 03/03/2021
 * @since 1.0
 */
class CBannerAddController extends ARestrictedAccessRootController
{
    /**
     * @var string
     */
    protected $fallBack = "blueseal";
    /**
     * @var string
     */
    protected $pageSlug = "banner_add";

    /**
     * @throws \bamboo\core\exceptions\RedPandaORMException
     */
    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/banner_add.php');

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'page'=>$this->page,
            'sidebar'=>$this->sidebar->build()
        ]);
    }

    /**
     * @return int
     */
    public function post()
    {
        try {
            $data = $this->app->router->request()->getRequestData();

            $banner = \Monkey::app()->repoFactory->create('Banner')->getEmptyEntity();
            $banner->campaignId = $data['campaignId'];
            $banner->name = $data['name'];
            $banner->textHtml = $data['textHtml'];
            switch(true){
                case ($data['position'] % 4 ==1):
                    $position=1;
                    break;
                case ($data['position'] % 4 ==2):
                    $position=($data['position']-1);
                    break;
                case ($data['position'] % 4==3):
                    $position=($data['position']-2);
                    break;
                case ($data['position'] % 4 ==0):
                    $position=($data['position']+3);
                    break;

            }
            $banner->position = $position;
            $banner->link = $data['link'];
            $banner->remoteShopId = $data['remoteShopId'];
            $banner->isActive=$data['remoteShopId'];
            $campaign=\Monkey::app()->repoFactory->create('Campaign')->findOneBy(['id'=>$data['campaignId'],'remoteShopId'=>$data['remoteShopId']]);

            $remoteCampaignId=$campaign->remoteCampaignId;
            $findShopId = \Monkey::app()->repoFactory->create('Shop')->findOneBy(['id' => $data['remoteShopId']]);
            $db_host = $findShopId->dbHost;
            $db_name = $findShopId->dbName;
            $db_user = $findShopId->dbUsername;
            $db_pass = $findShopId->dbPassword;
            try {
                $db_con = new PDO("mysql:host={$db_host};dbname={$db_name}",$db_user,$db_pass);
                $db_con->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
                $res = " connessione ok <br>";
            } catch (PDOException $e) {
                throw new BambooException('fail to connect');

            }
            $stmtBannerInsert = $db_con->prepare('INSERT INTO Banner (`name`,`textHtml`,`position`,link,click,campaignId,isActive)
                VALUES(
                                 \'' . $data['name'] . '\',
                                 \'' . $data['textHtml'] . '\',
                                 \'' . $data['position'] . '\',
                                 \'' . $data['link'] . '\',
                                 \'0\',
                                  \''. $remoteCampaignId.'\',
                                  \''. $data['isActive'].'\'
                                    )');
            $stmtBannerInsert->execute();
            $remoteId = $db_con->lastInsertId();
            $banner->remoteId=$remoteId;

            return $banner->insert();
        } catch (\Throwable $e) {
            $this->app->router->response()->raiseProcessingError();
            $this->app->router->response()->sendHeaders();
        }
    }
}