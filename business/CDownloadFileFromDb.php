<?php
namespace bamboo\blueseal\business;

use bamboo\core\exceptions\BambooRoutingException;
use bamboo\domain\entities\CUser;

/**
 * Class CCouponListController
 * @package bamboo\app\controllers
 */
class CDownloadFileFromDb
{
    protected $table = '';
    protected $field = '';
    protected $value = '';

    public function __construct($table, $field, $value)
    {
        $this->table = $table;
        $this->field = $field;
        $this->value = $value;
    }

    public function getFile()
    {
        /** @var CUser $user */

        $table = $this->table;
        $field = $this->field;
        $value = $this->value;
        switch ($table) {
            case 'InvoiceBin':
                $res = $this->getInvoiceBin($table, $field, $value);
                break;
            case 'Contracts':
                $res = $this->getContractsBin($table, $field, $value);
                break;
            default:
                $res = [];
                $res['bin'] = \Monkey::app()->repoFactory->create($table)->findOneBy([$field => $value])->fetch()->bin;
                $finfo = new finfo(FILEINFO_MIME);
                $res['mime'] = $finfo->buffer($res['bin']);
        }
        \Monkey::app()->router->response()->setContentType($res['mime']);
        return $res['bin'];
    }

    /**
     * @param $table
     * @param $field
     * @param $value
     * @return array
     * @throws BambooRoutingException
     */
    public function getInvoiceBin($table, $field, $value)
    {
        $res = [];
        $user = \Monkey::app()->getUser();

        $ib = \Monkey::app()->repoFactory->create($table)->findOneBy([$field => $value]);

        if (!$ib) throw new BambooRoutingException('File Not Found');
        if(!$user->hasPermission("shooting")){
            if(!$user->hasPermission("worker")){
                if (!$user->hasShop($ib->document->shopAddressBook->shop->id)) throw new BambooRoutingException('NotAuthorized');
            }
        }


        $res['bin'] = $ib->bin;
        $finfo = new \finfo(FILEINFO_MIME);
        $res['mime'] = $finfo->buffer($res['bin']);
        return $res;
    }

    /**
     * @param $table
     * @param $field
     * @param $value
     * @return array
     * @throws BambooRoutingException
     */
    public function getContractsBin($table, $field, $value)
    {
        $res = [];
        $user = \Monkey::app()->getUser();

        $ib = \Monkey::app()->repoFactory->create($table)->findOneBy([$field => $value]);

        if (!$ib) throw new BambooRoutingException('File Not Found');
        if(!$user->hasPermission("worker")) throw new BambooRoutingException('NotAuthorized');

        $res['bin'] = $ib->bin;
        $finfo = new \finfo(FILEINFO_MIME);
        $res['mime'] = $finfo->buffer($res['bin']);
        return $res;
    }
}