<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\domain\repositories\CDocumentRepo;
use bamboo\ecommerce\views\VBase;
use bamboo\utils\time\STimeToolbox;

/**
 * Class CDocumentAddCustomController
 * @package bamboo\blueseal\controllers
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
class CDocumentCustomAddController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "document_custom_add";

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/document_custom_add.php');

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'page'=>$this->page,
            'sidebar' => $this->sidebar->build()
        ]);
    }

    public function post()
    {
        $data = $this->app->router->request()->getRequestData();
        $files = $this->app->router->request()->getFiles();

        /** @var CDocumentRepo $documentRepo */
        $documentRepo = $this->app->repoFactory->create('Document');

        $rows = [];
        foreach ($data as $key => $row) {
            $val = explode('_',$key);
            if($val[0] != 'row') continue;
            if(!isset($rows[$val[1]])) $rows[$val[1]] = [];
            $rows[$val[1]][$val[2]] = $row;
        }
        $this->app->repoFactory->beginTransaction();
        $documentId = $documentRepo->storeNewCustomInvoice(
            (int) $data['invoiceTypeId'],
            $this->app->getUser()->id,
            (int) $data['shopRecipientId'],
            STimeToolbox::GetDateTime($data['date']),
            (float) $data['totalWithVat'],
            STimeToolbox::GetDateTime($data['paymentExpectedDate']),
            $data['number'],
            $data['note'] ?? "",
            $rows,
            false,
            $files['invoiceBin']['name'],
            $files['invoiceBin']['tmp_name']);

        $this->app->repoFactory->commit();
    }
}