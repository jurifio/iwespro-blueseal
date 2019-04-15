<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\utils\slugify\CSlugify;
use bamboo\domain\entities\CFixedPage;
use bamboo\domain\entities\CFixedPagePopup;
use bamboo\domain\repositories\CFixedPagePopupRepo;
use bamboo\domain\repositories\CFixedPageRepo;


/**
 * Class CManageFixedPageAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 19/02/2019
 * @since 1.0
 */
class CManageFixedPageAjaxController extends AAjaxController
{
    /**
     * @throws \bamboo\core\exceptions\BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function put()
    {
        /** @var CFixedPageRepo $fixedPageRepo */
        $fixedPageRepo = \Monkey::app()->repoFactory->create('FixedPage');

        $slugify = new CSlugify();

        $title = (empty($this->data['title'])) ? null : $this->data['title'];
        $subtitle = (empty($this->data['subtitle'])) ? null : $this->data['subtitle'];

        $fixedPage = $fixedPageRepo->updateFixedPage(
            $this->data['id'],
            $this->data['fixedPageTypeId'],
            $this->data['lang'],
            $title,
            $subtitle,
            $slugify->slugify($this->data['slug']),
            $this->data['text'],
            $this->data['titleTag'],
            $this->data['metaDescription']
        );

        /** @var CFixedPagePopupRepo $fPPRepo */
        $fPPRepo = \Monkey::app()->repoFactory->create('FixedPagePopup');
        if ($this->data['popupUse'] === 'true') {

            if (!empty($this->data['popupId'])) {
                $fPPRepo->updateFixedPagePopup(
                    $this->data['popupId'],
                    $this->data['popupTitle'],
                    $this->data['popupSubTitle'],
                    $this->data['popupText'],
                    $this->data['couponEvent']);
            } else {
                $fPPRepo->insertNewPopup(
                    $this->data['popupTitle'],
                    $this->data['popupSubTitle'],
                    $this->data['popupText'],
                    $this->data['couponEvent'],
                    1,
                    $fixedPage->id
                );
            }

        } else {
            if (!empty($this->data['popupId'])) {
                $fPPRepo->deactivateFixedPagePopup($this->data['popupId']);
            }
        }

        return 'put';
    }

    /**
     * @return array|bool
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    public function post()
    {
        /** @var CFixedPageRepo $fixedPageRepo */
        $fixedPageRepo = \Monkey::app()->repoFactory->create('FixedPage');

        $slugify = new CSlugify();

        $title = (empty($this->data['title'])) ? null : $this->data['title'];
        $subtitle = (empty($this->data['subtitle'])) ? null : $this->data['subtitle'];
        /** @var CFixedPage $fixedPage */
        $fixedPage = $fixedPageRepo->insertFixedPage(
            $this->data['fixedPageTypeId'],
            $this->data['lang'],
            $title,
            $subtitle,
            $slugify->slugify($this->data['slug']),
            urldecode($this->data['text']),
            $this->data['titleTag'],
            $this->data['metaDescription']
        );

        if ($this->data['popupUse'] === 'true') {

            /** @var CFixedPagePopupRepo $fPPRepo */
            $fPPRepo = \Monkey::app()->repoFactory->create('FixedPagePopup');
            $fPPRepo->insertNewPopup(
                $this->data['popupTitle'],
                $this->data['popupSubTitle'],
                $this->data['popupText'],
                $this->data['couponEvent'],
                1,
                $fixedPage->id
            );
        }

        if ($fixedPage) {
            $res = [];
            $res['id'] = $fixedPage->id;
            $res['langId'] = $fixedPage->langId;
            $res['fixedPageTypeId'] = $fixedPage->fixedPageTypeId;

            return json_encode($res);
        }

        return false;
    }

}