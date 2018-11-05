<?php
namespace bamboo\blueseal\controllers;

use bamboo\blueseal\business\CBlueSealPage;
use bamboo\blueseal\business\CDownloadFileFromDb;
use bamboo\core\exceptions\BambooInvoiceException;
use bamboo\core\exceptions\BambooRoutingException;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\domain\entities\CDocument;
use bamboo\ecommerce\views\VBase;
use bamboo\utils\price\SPriceToolbox;

/**
 * Class CCouponListController
 * @package bamboo\app\controllers
 */
class CDownloadContracts extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "download_contracts";

    public function get()
    {
        $filters = \Monkey::app()->router->getMatchedRoute()->getComputedFilters();
        $type = \Monkey::app()->router->request()->getRequestData('type');
        $i = \Monkey::app()->repoFactory->create($type)->findOne([$filters['id']]);
        try {
            $user = \Monkey::app()->getUser();
            if(!$user->hasPermission("worker")) throw new BambooRoutingException('Not Authorized');

            if (!$i) throw new BambooRoutingException('File Not Found');
            if ($i->bin) {
                $download = new CDownloadFileFromDb($type, 'id', $filters['id']);
                $ret = $download->getFile();
            } else if (!$i->bin){
                $ret = "Non è associato nessun file";
            }
            echo $ret;
        } catch (BambooRoutingException $e) {
            if ('File Not Found' === $e->getMessage()) \Monkey::app()->router->response()->raiseRoutingError();
            elseif ('Not Authorized' === $e->getMessage()) \Monkey::app()->router->response()->raiseUnauthorized();
        }
    }
}