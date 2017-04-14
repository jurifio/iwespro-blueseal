<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\application\AApplication;
use bamboo\core\exceptions\BambooInvoiceException;
use bamboo\core\intl\CLang;
use bamboo\domain\entities\CPaymentBill;
use bamboo\domain\entities\CProduct;
use bamboo\utils\time\STimeToolbox;

/**
 * Class CPaymentBillListAjaxController
 * @package bamboo\blueseal\controllers\ajax
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
class CJobManage extends AAjaxController
{
    /**
     * @return string
     */
    public function get() {
        $jobId = $this->app->router->request()->getRequestData('jobId');
        $job = $this->app->repoFactory->create('Job')->findOneByStringId($jobId);
        return json_encode($job);
    }

    public function put()
    {
        $jobData = $this->app->router->request()->getRequestData('job');
        /** @var CPaymentBill $paymentBill */
        $job = $this->app->repoFactory->create('Job')->findOneByStringId($jobData['id']);

        $job->manualStart = $jobData['manualStart'] ?? $job->manualStart;
        $job->update();
        return true;
    }
}