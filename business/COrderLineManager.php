<?php


namespace bamboo\blueseal\business;

use bamboo\core\ecommerce\IBillingLogic;
use bamboo\domain\entities\COrderLine;
use bamboo\domain\entities\COrderLineStatus;
use bamboo\core\application\AApplication;
use bamboo\domain\repositories\COrderLineStatusRepo;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\domain\entities\CProductSku;
use bamboo\core\exceptions\RedPandaException;

class COrderLineManager
{
    /**
     * @var AApplication
     */
    protected $app;
    /**
     * @var COrderLine
     */
    protected $orderLine;

    /**
     * @var int
     */
    protected $defaultSku;
    
    /**
     * @param AApplication $app
     * @param COrderLine $orderLine
     */
    public function __construct(AApplication $app, COrderLine $orderLine)
    {
        $this->app = $app;
        $this->orderLine = $orderLine;
    }



}