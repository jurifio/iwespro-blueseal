<?php

namespace bamboo\blueseal\jobs;


use DateTime;
use PDO;
use prepare;
use bamboo\domain\repositories\CEmailRepo;
use bamboo\core\jobs\ACronJob;
use bamboo\domain\entities\CProductPublicSku;
use bamboo\domain\entities\CProduct;
use bamboo\core\events\AEventListener;
use bamboo\domain\repositories\CMarketplaceAccountHasProductRepo;
use bamboo\domain\repositories\CProductRepo;





class CDeactivateCampaignAggregatorJob extends ACronJob
{
    /**
     * @param null $args
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    public function run($args = null)
    {

        set_time_limit(0);
        ini_set('memory_limit','2048M');

        $res = "";
        /********marketplace********/
        $db_host = "5.189.159.187";
        $db_name = "iwesPrestaDB";
        $db_user = "pickyshop4";
        $db_pass = "rrtYvg6W!";
        try {

            $db_con = new PDO("mysql:host={$db_host};dbname={$db_name}",$db_user,$db_pass);
            $db_con->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            $res .= " connessione ok <br>";
        } catch (PDOException $e) {
            $res .= $e->getMessage();
        }
        $campaignRepo = \Monkey::app()->repoFactory->create('Campaign');
        $marketplaceAccountRepo = \Monkey::app()->repoFactory->create('MarketplaceAccount');
        $productBrandRepo = \Monkey::app()->repoFactory->create('ProductBrand');
        /** @var CProductRepo $productRepo */
        $productRepo = \Monkey::app()->repoFactory->create('Product');
        $productSkuRepo = \Monkey::app()->repoFactory->create('ProductSku');
        $marketplaceRepo = \Monkey::app()->repoFactory->create('Marketplace');

        $campaigns=$campaignRepo->findAll();
        foreach ($campaigns as $campaign){
            if($campaign->marketplaceAccountId==null && $campaign->marketplaceId==null){
                continue;
            }else{
                $marketplace=$marketplaceRepo->findOneBy(['id'=>$campaign->marketplaceId]);
                $typeMarketplace=$marketplace->type;
                if($typeMarketplace=='cpc'){
                    $marketplaceAccount=$marketplaceAccountRepo->findOneBy(['id'=>$campaign->marketplaceAccountId,'marketplaceId'=>$campaign->marketplaceId]);
                    $currentMonth = (new DateTime())->format('m');
                    $year=(new DateTime())->format('Y');
                    switch($currentMonth){
                        case "01":
                            $budget=$marketplaceAccount->config['budget01'];
                            $lastDay='31';
                            break;
                        case "02":
                            $budget=$marketplaceAccount->config['budget02'];
                            $lastDay='28';
                            break;
                        case "03":
                            $budget=$marketplaceAccount->config['budget03'];
                            $lastDay='31';
                            break;
                        case "04":
                            $budget=$marketplaceAccount->config['budget04'];
                            $lastDay='30';
                            break;
                        case "05":
                            $budget=$marketplaceAccount->config['budget05'];
                            $lastDay='31';
                            break;
                        case "06":
                            $budget=$marketplaceAccount->config['budget06'];
                            $lastDay='30';
                            break;
                        case "07":
                            $budget=$marketplaceAccount->config['budget07'];
                            $lastDay='31';
                            break;
                        case "08":
                            $budget=$marketplaceAccount->config['budget08'];
                            $lastDay='31';
                            break;
                        case "09":
                            $budget=$marketplaceAccount->config['budget09'];
                            $lastDay='30';
                            break;
                        case "10":
                            $budget=$marketplaceAccount->config['budget10'];
                            $lastDay='31';
                            break;
                        case "11":
                            $budget=$marketplaceAccount->config['budget11'];
                            $lastDay='30';
                            break;
                        case "12":
                            $budget=$marketplaceAccount->config['budget12'];
                            $lastDay='31';
                            break;
                    }
                    $sql = "select sum(cost) as cost FROM CampaignVisit WHERE campaignId=".$campaign->id. " AND timestamp between '".$year."-".$currentMonth."-01 00:00:00' and  '".$year."-".$currentMonth."-".$lastDay." 00:00:00'";
                    $cost = \Monkey::app()->dbAdapter->query($sql, [])->fetchAll()[0]['cost'];
                    if($cost>=$budget){
                        $marketplaceAccount->isActive=0;
                        $marketplaceAccount->update();
                        $this->report('CDeactivateCampaignAggregatorJob','deactivate Aggregator'.$marketplaceAccount->name,'');
                        $mailRepo = \Monkey::app()->repoFactory->create('Email');
                        $bodyMail='Aggregatore '.$marketplaceAccount->name. 'ha raggiunto il budget di euro  per il corrente mese<br>la pubblicazione è stata disattivata';
                        $mailRepo->newMail('it@iwes.it',["gianluca@iwes.it","juri@iwes.it","it@iwes.it"],[],[],"Dissattivazione  " . $marketplaceAccount->name,$bodyMail);
                        $aggregatorPublishLog=\Monkey::app()->repoFactory->create('AggregatorPublishLog')->getEmptyEntity();
                        $aggregatorPublishLog->marketplaceAccountId=$markeplaceAccountId;
                        $aggregatorPublishLog->marketplaceId=$marketplaceId;
                        $aggregatorPublishLog->subject="Disattivazione Pubblicazione su " . $marketplaceAccount->name;
                        $aggregatorPublishLog->result="success";
                        $aggregatorPublishLog->action='Depublish';
                        $aggregatorPublishLog->email="gianluca@iwes.it;juri@iwes.it;it@iwes.it";
                        $aggregatorPublishLog->insert();
                    }

                }
            }
        }

    }


}