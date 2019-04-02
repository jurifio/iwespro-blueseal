<?php

namespace bamboo\controllers\back\ajax;

use Aws\DynamoDb\Model\Attribute;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\email\CEmail;
use bamboo\core\exceptions\BambooException;
use bamboo\core\exceptions\BambooORMInvalidEntityException;
use bamboo\core\exceptions\BambooORMReadOnlyException;
use bamboo\domain\entities\CEmailAddress;
use bamboo\domain\entities\CNewsletter;
use bamboo\domain\entities\CNewsletterCampaign;
use bamboo\domain\entities\CNewsletterEvent;
use bamboo\domain\entities\CNewsletterInsertion;
use bamboo\domain\entities\CNewsletterTemplate;
use bamboo\domain\entities\CNewsletterUser;
use bamboo\domain\repositories\CEmailRepo;
use bamboo\domain\repositories\CNewsletterRepo;
use bamboo\domain\entities\CNewsletterGroup;



/**
 * Class CnewsletterUserManage
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
class CGetNumberContactEmailListAjaxController extends AAjaxController
{
    /**
     * @return int
     * @throws BambooException
     * @throws \Exception
     */
    public function get()
    {
        //prendo i dati passati in input
        $data = \Monkey::app()->router->request()->getRequestData();
        $idnewsletterListId = $data['idnewsletterEmailList'];
        /* @var $newsletterEmailList $NewsletterEmailList
         * */
        $newsletterEmailList=\Monkey::app()->repoFactory->create('NewsletterEmailList')->findOneBy(['id'=>$idnewsletterListId]);
        $newsletterGroupId=$newsletterEmailList->newsletterGroupId;
        $newsletterGroup=\Monkey::app()->repoFactory->create('NewsletterGroup')->findOneBy(['id'=>$newsletterGroupId]);
        $sql=$newsletterGroup->sql;
        $row=\Monkey::app()->dbAdapter->query($sql, [])->fetchAll();
        $i=0;
        foreach ($row as $rows){

            $i++;
        }
$res="<b>TOTALE CONTATTI:". $i."</b>";


        return $res;
    }


}