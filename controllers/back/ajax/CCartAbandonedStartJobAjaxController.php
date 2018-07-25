<?php
/**
 *
 */
namespace bamboo\controllers\back\ajax;

use bamboo\core\db\pandaorm\repositories\ARepo;
use bamboo\core\jobs\ACronJob;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\domain\entities\CWishList;
use bamboo\domain\entities\CUser;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CCart;
use bamboo\domain\entities\CProductPublicSku;
use bamboo\domain\entities\CCartLine;
use bamboo\core\base\CSerialNumber;
use bamboo\domain\entities\CJob;
use bamboo\domain\entities\CCartAbandonedEmailSend;
use bamboo\domain\repositories\CCartAbandonedEmailSendRepo;
use bamboo\utils\time\STimeToolbox;
use bamboo\utils\price\SPriceToolbox;
use bamboo\core\events\AEventListener;

class CCartAbandonedStartJobAjaxController extends AAjaxController
{

    /**
     *
     */
    public function post()
    {
        $data = \Monkey::app()->router->request()->getRequestData();
        $checkJob=$data['start'];
        $job=\Monkey::app()->repoFactory->create('Job')->findOneBy(['id'=>'86']);
        $job->isActive='1';
        $job->update();
        $res='Job Carrelli Abbandonati';
        return $res;
    }







}