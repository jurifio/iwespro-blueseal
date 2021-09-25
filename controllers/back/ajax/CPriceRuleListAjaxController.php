<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\intl\CLang;


/**
 * Class CPriceRuleListAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 15/09/2021
 * @since 1.0
 */
class CPriceRuleListAjaxController extends AAjaxController
{
    public function get()
    {
        $sql = "SELECT
                
                  `p`.`id`                                          AS `id`,
                  `p`.`name`                            AS `name`,
                  `s`.`name`                                AS `shopName`,
                   `p`.shopId as shopId, 
                  if(`p`.`typeVariation`=1,'Sconto','Maggiorazione')                                      AS `typeVariation`,
                  `p`.`variation`                                      AS `variation`,
                  if(`p`.`typeVariationSale`=1,'Sconto','Maggiorazione')                                      AS `typeVariationSale`,
                  `p`.`variationSale`                                      AS `variationSale`,
                  `p`.`dateStart`                                      AS `dateStart`,
                   `p`.`dateEnd`                                      AS `dateEnd`

                
                FROM `PriceRuleList` `p` join Shop s on s.id=p.shopId";

        $datatable = new CDataTables($sql, ['id','shopId'], $_GET);

        $priceRuleList = \Monkey::app()->repoFactory->create('PriceRuleList')->em()->findBySql($datatable->getQuery(), $datatable->getParams());
        $shopRepo=\Monkey::app()->repoFactory->create('Shop');
        $count = \Monkey::app()->repoFactory->create('PriceRuleList')->em()->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totalCount = \Monkey::app()->repoFactory->create('PriceRuleList')->em()->findCountBySql($datatable->getQuery('full'), $datatable->getParams());


        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];

        $i = 0;

        foreach ($priceRuleList as $v) {
            try {
                $response['data'][$i]["DT_RowId"] =  $v->id;
                $response['data'][$i]['id'] = $v->id;
                $response['data'][$i]['name'] = '<a href="/blueseal/listini/modifica/'.$v->id.'/'.$v->shopId.'/">'.$v->name.'</a>';
                $response['data'][$i]['typeVariation'] = ($v->typeVariation==1) ? 'Sconto':'Maggiorazione';
                $response['data'][$i]['variation'] = $v->variation;
                $response['data'][$i]['typeVariationSale'] = ($v->typeVariation==1) ? 'Sconto':'Maggiorazione';
                $response['data'][$i]['variationSale'] = $v->variation;
                $shop=$shopRepo->findOneBy(['id'=>$v->shopId]);
                $response['data'][$i]['shopName'] = $shop->name;
                $response['data'][$i]['shopId'] = $v->shopId;
                $response['data'][$i]['dateStart'] = (new \DateTime($v->dateStart))->format('d-m-Y');
                $response['data'][$i]['dateEnd'] = (new \DateTime($v->dateEnd))->format('d-m-Y');
                $i++;
            } catch (\Throwable $e) {
                throw $e;
            }
        }
        return json_encode($response);
    }
    public function delete(){
        try {
            $data = \Monkey::app()->router->request()->getRequestData();
            /** @var CRepo $priceListRepo */
            $priceListRepo = \Monkey::app()->repoFactory->create('PriceRuleList');
            $id = $data['id'];
            $shopId = $data['shopId'];
            $remoteId = $data['remoteId'];
            /** @var CPriceList $pc */
            $pc = $priceListRepo->findOneBy(['id' => $id,'shopId'=>$shopId]);
            $pc->delete();
            $findShopId = \Monkey::app()->repoFactory->create('Shop')->findOneBy(['id' => $shopId]);
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
            $stmtDeletePc = $db_con->prepare("DELETE FROM PriceRuleList 
                                where id=" . $id.' and shopId='.$shopId);
            $stmtDeletePc->execute();


            return 'Regola Listino   Cancellata con successo';
        }catch(\Throwable $e){
            \Monkey::app()->applicationLog('CPriceListListAjaxController','Error','Delete PriceRuleList',$e->getMessage(),$e->getLine());
            return 'Error TraceLog '.$e->getMessage();
        }
    }




}