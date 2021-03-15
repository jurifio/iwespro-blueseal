<?php

namespace bamboo\blueseal\controllers;

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;
use bamboo\domain\entities\CProduct;


/**
 * Class CShopListController
 * @package bamboo\blueseal\controllers
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date $date
 * @since 1.0
 */
class CAggregatorAccountController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "aggregator-account_edit";

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath() . $this->app->cfg()->fetch('paths','blueseal') . '/template/aggregator_account_edit.php');
        $marketplaceAccountGet = \Monkey::app()->router->request()->getRequestData('id');
        $marketplaceCode = explode('-',$marketplaceAccountGet);


        $marketplaceAccount = \Monkey::app()->repoFactory->create('MarketplaceAccount')->findOneBy(['id' => $marketplaceCode[0],'marketplaceId' => $marketplaceCode[1]]);

        $marketplaceConfig = json_encode($marketplaceAccount->config,false);
        $countConfig = json_decode($marketplaceConfig,true);
        $countField = count($countConfig);


        $aggregatorHasShop=\Monkey::app()->repoFactory->create('AggregatorHasShop')->findAll();
        $optionAggregator='';
        foreach($aggregatorHasShop as $shop){
            if($shop->id == $marketplaceAccount->config['aggregatorHasShopId']){
                $optionAggregator.='<option selected="selected" value="'.$shop->id.'">'.$shop->name.'</option>';
            }else{
                $optionAggregator.='<option  value="'.$shop->id.'">'.$shop->name.'</option>';
            }

        }


        $shopId =$marketplaceAccount->config['shopId'];
        $res = $this -> app -> dbAdapter -> query('(SELECT pb.id as id,
        pb.name as brandName, s.name as shopName, s.id as shopIdOrigin, s.id AS shopIdDestination from ProductBrand pb
                                                      join Product p on pb.id=p.productBrandId
                                                      join  ProductSku ps on p.id =ps.productId and p.productVariantId=ps.productVariantId
                                                      join Shop s on ps.shopId=s.id
 WHERE ps.shopId = '.$shopId.' group by pb.name,ps.shopId)
UNION
(SELECT pb.id as id,
        pb.name as brandName,
        s.name as  shopName,
        ps.shopIdOrigin as shopIdOrigin, ps.shopIdDestination AS shopDestination from ProductBrand pb
                                                  join Product p on pb.id=p.productBrandId
                                                  join  ProductHasShopDestination ps on p.id =ps.productId and p.productVariantId=ps.productVariantId
                                                  join Shop s on ps.shopIdOrigin=s.id
 WHERE ps.shopIdDestination = '.$shopId.' and ps.shopIdOrigin <> '.$shopId.' group by pb.name, shopIdOrigin)', []) -> fetchAll();
$bodyres='<div class="row"><div class="col-md-4"><input type="text" id="myInput" onkeyup="myFunction()" placeholder="ricerca Brand"></div>';
$bodyres.='<div class="col-md-4"><input type="text" id="myShop" onkeyup="myShopFunction()" placeholder="ricerca per Shop"></div>';
$bodyres.='<div class="col-md-4"><input type="checkbox" class="form-control"  id="checkedAll" name="checkedAll"></div></div>';
       $bodyres.= '<table id="myTable"> <tr class="header1"><th style="width:40%;">Categoria</th><th style="width:40%;">Shop</th><th style="width:20%;">Selezione</th></tr>';
        foreach ($res as $result) {
           // $selectBrands[] = ['id' => $result['id'], 'brandName' => $result['brandName'], 'shopName' => $result['shopName'], 'shopIdOrigin' => $result['shopIdOrigin'], 'shopIdDestination' => $result['shopIdDestination']];
            $bodyres.='<tr><td style="width:40%;">' . $result['brandName']. '</td><td style="width:40%;">' . $result['shopName'] . '</td><td style="width:20%;"><input type="checkbox" class="form-control"  name="selected_values[]" value="'.$result['id'].'-'.$result['shopIdOrigin'].'"></td></tr>';
        }
$bodyres.='</table>';

$campaigns=\Monkey::app()->repoFactory->create('Campaign')->findOneBy(['marketplaceId'=>$marketplaceAccount->marketplaceId,'marketplaceAccountId'=>$marketplaceAccount->id]);
$campaignOption='';

    if(!$campaigns) {
        $campaignOption='';
    }else{
        $campaignOption = $campaigns->id;
    }



        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'page' => $this->page,
            'marketplaceAccountGet' => $marketplaceAccountGet,
            'optionAggregator'=>$optionAggregator,
            'marketplaceAccount' => $marketplaceAccount,
            'marketplaceConfig' => $marketplaceConfig,
            'marketplaceCode' => $marketplaceCode,
            'countField' => $countField,
            'bodyres'=>$bodyres,
            'campaignOption'=>$campaignOption,
            'sidebar' => $this->sidebar->build()
        ]);
    }
}