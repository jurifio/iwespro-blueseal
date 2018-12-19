<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\domain\entities\CFaq;
use bamboo\domain\entities\CFoison;
use bamboo\domain\entities\CProductBatch;
use bamboo\domain\entities\CProductBatchTextManage;
use bamboo\domain\entities\CProductBatchTextManagePhoto;
use bamboo\domain\entities\CProductCardPhoto;
use bamboo\domain\entities\CProductSheetModelPrototypeCategoryGroup;
use bamboo\domain\repositories\CFaqRepo;
use bamboo\domain\repositories\CFoisonRepo;
use bamboo\domain\repositories\CProductBatchRepo;
use bamboo\domain\repositories\CProductBatchTextManageRepo;
use bamboo\ecommerce\views\VBase;
use bamboo\core\exceptions\RedPandaAssetException;
use bamboo\core\exceptions\RedPandaException;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\core\utils\amazonPhotoManager\ImageManager;
use bamboo\core\utils\amazonPhotoManager\S3Manager;

/**
 * Class CProductWorkFaqAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 19/12/2018
 * @since 1.0
 */
class CProductWorkFaqAjaxController extends AAjaxController
{

    /**
     * @return string
     */
    public function post() : string
    {
        /** @var CFaq $faq */
        $faq = \Monkey::app()->repoFactory->create('Faq')->getEmptyEntity();
        $faq->question = $this->data['q'];
        $faq->answer = $this->data['a'];
        $faq->faqTypeId = 1;
        $faq->smartInsert();

        return 'Faq inserita con successo';
    }

    public function get()
    {

        /** @var CFaqRepo $faqRepo */
        $faqRepo = \Monkey::app()->repoFactory->create('Faq');

        $faqs = $faqRepo->searchFaq($this->data['search']);

        return json_encode($faqs);
    }
}