<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\domain\entities\CGainPlanPassiveMovement;

/**
 * Class CInvoiceManage
 * @package bamboo\controllers\back\ajaxù
 *
 */
class CInvoiceManage extends AAjaxController
{
    /**
     * @return string
     */
    public function post()
    {

        $request = \Monkey::app()->router->request();
        $invoice = $request->getRequestData('invoice');

    }

    /**
     * @return string
     * @throws \bamboo\core\exceptions\BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function put()
    {



    }

    /**
     * @return string
     * @throws \bamboo\core\exceptions\BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function delete()
    {

    }
}