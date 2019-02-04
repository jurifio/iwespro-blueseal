<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\domain\entities\CInvoiceType;
use bamboo\domain\repositories\CDocumentRepo;
use bamboo\domain\repositories\CEmailRepo;
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
        $documentRepo = \Monkey::app()->repoFactory->create('Document');

        $rows = [];
        foreach ($data as $key => $row) {
            $val = explode('_',$key);
            if($val[0] != 'row') continue;
            if(!isset($rows[$val[1]])) $rows[$val[1]] = [];
            $rows[$val[1]][$val[2]] = $row;
        }
        \Monkey::app()->repoFactory->beginTransaction();
        try {
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
                true,
                $files['invoiceBin']['name'],
                $files['invoiceBin']['tmp_name']);

            if($data['invoiceTypeId'] == CInvoiceType::CREDIT_REQUEST){

                $shopEmail = \Monkey::app()->repoFactory->create('Shop')->findOneBy(['billingAddressBookId'=>$data['shopRecipientId']])->amministrativeEmails;

                $body = '
                <img height="300px" src="https://www.pickyshop.com/it/assets/logoiwes.png">
                 <br />
                 <br />
                 Salve,
                <br />
                Preghiamo prendere nota che la vostra richiesta di accredito pari a 20€ è stata inserita nella distinta
                n. 30 e compensata con i pagamenti da voi dovuti.
                <br />
                Cordialmente,<br />
                <br />
                <img width="40px" src="https://www.pickyshop.com/it/assets/Iwes.png">
                <br />
                 <br />
                Billing Department
                ';

                /** @var CEmailRepo $emailRepo */
                $emailRepo = \Monkey::app()->repoFactory->create('Email');
                $emailRepo->newMail('no-reply@pickyshop.com', [$shopEmail], [], [], 'Richiesta di accredito', $body);

            }
        } catch (\Throwable $e) {
            \Monkey::app()->repoFactory->rollback();
            throw $e;
        }

        \Monkey::app()->repoFactory->commit();
    }
}