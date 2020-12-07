<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CEditorialPlan;
use bamboo\domain\entities\CNewsletterCampaign;
use bamboo\domain\repositories\CNewsletterCampaignRepo;
use bamboo\domain\entity;


/**
 * Class CEditorialPlanManage
 * @package bamboo\controllers\back\ajax
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
class CTagAllExclusiveManage extends AAjaxController
{
    /**
     * @return int
     * @throws BambooException
     * @throws \Exception
     */
    public function post()
    {
        try {

            $res='';
            //prendo i dati passati in input
            $data = \Monkey::app()->router->request()->getRequestData();
            $tagExclusive = \Monkey::app()->repoFactory->create('TagExclusive')->findOneBy(['slug' => 'all']);
            $tagExclusiveId = $tagExclusive->id;
            $ProductHasTagExclusiveRepo = \Monkey::app()->repoFactory->create('ProductHasTagExclusive');
            $res = \Monkey::app()->dbAdapter->query('SELECT p.id, p.productVariantId from Product p where p.qty>0', []) -> fetchAll();

            foreach ($res as $result) {

                $phte = $ProductHasTagExclusiveRepo->findOneBy(['productId' => $result['id'],'productVariantId' => $result['productVariantId']]);
                if ($phte) {
                    continue;
                } else {
                    $phteInsert = $ProductHasTagExclusiveRepo->getEmptyEntity();
                    $phteInsert->productId = $result['id'];
                    $phteInsert->productVariantId = $result['productVariantId'];
                    $phteInsert->tagExclusiveId = $tagExclusiveId;
                    $phteInsert->insert();
                }
            }
            $res="ok";
        }catch(\Throwable $e){
            \Monkey::app()->applicationLog('CTagAllExclusiveManage','Error','Insert Tag Exclusive all error',$e->getLine(),$e->getMessage());
            $res="ko". $e->getMessage();
        }
        return $res;
    }


}