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
use FacebookAds\Object\AdAccount;
use FacebookAds\Object\Ad;
use FacebookAds\Object\Fields\AdFields;
use FacebookAds\Api;
use FacebookAds\Logger\CurlLogger;
use FacebookAds\Object\AdUser;
use FacebookAds\Object\Fields\AdAccountFields;
use FacebookAds\Object\Campaign;
use FacebookAds\Object\AdCampaign;
use FacebookAds\Object\Fields\CampaignFields;
use FacebookAds\Object\Fields\AdsInsightsFields;
use FacebookAds\Object\AdSet;
use FacebookAds\Object\Fields\AdSetFields;


class CSelectOtherShopBrandsAjaxController extends AAjaxController
{


    public function get()
    {

        $brands = [];
        $data = $this->app->router->request()->getRequestData();
        $shopId = $data['shopId'];

        $sql = "select pb.id as id, 
              pb.name as name,
              if(pb.hasMarketplaceRights='1','si','no') as hasMarketplaceRights,
              if(pb.hasAggregator='1','si','no') as hasAggregator
              from ProductBrand pb join Product p on pb.id=p.productBrandId join ShopHasProduct shp on p.id=shp.productId and p.productVariantId=shp.productVariantId
              where shp.shopId!=? group by pb.id";
        $res = \Monkey::app()->dbAdapter->query($sql,[$shopId])->fetchAll();
        foreach ($res as $val) {
            $brands[] = ['id' => $val['id'],
                'name' => $val['name'],
                'hasMarketplaceRights' => $val['hasMarketplaceRights'],
                'hasAggregator' => $val['hasAggregator']
            ];
        }


        return json_encode($brands);
    }
}