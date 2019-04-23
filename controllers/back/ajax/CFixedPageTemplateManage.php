<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\domain\entities\CFixedPageTemplate;


/**
 * Class CFixedPageTemplateManage
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 23/04/2019
 * @since 1.0
 */
class CFixedPageTemplateManage extends AAjaxController
{
    /**
     * @return string
     */
    public function post()
    {
        /** @var CRepo $fixedPageTemplateRepo */
        $fixedPageTemplateRepo = \Monkey::app()->repoFactory->create('FixedPageTemplate');

        if(is_null($fixedPageTemplateRepo->findOneBy(['name'=>$this->data['name']]))){

            $fixedPageTemplate = $fixedPageTemplateRepo->getEmptyEntity();
            $fixedPageTemplate->name = $this->data['name'];
            $fixedPageTemplate->template = $this->data['template'];
            $fixedPageTemplate->smartInsert();

            return $fixedPageTemplate->id;

        } else return 'Esiste un template con lo stesso nome';

    }

    /**
     * @return string
     * @throws \bamboo\core\exceptions\BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function put()
    {
        /** @var CFixedPageTemplate $fixedPageTemplate */
        $fixedPageTemplate = \Monkey::app()->repoFactory->create('FixedPageTemplate')->findOneBy(['id'=>$this->data['id']]);

        $fixedPageTemplate->name = $this->data['name'];
        $fixedPageTemplate->template = $this->data['template'];
        $fixedPageTemplate->update();

        return 'Template aggiornato con successo';
    }

    /**
     * @return string
     * @throws \bamboo\core\exceptions\BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function delete(){

        /** @var CRepo $fixedPageTemplateRepo */
        $fixedPageTemplateRepo = \Monkey::app()->repoFactory->create('FixedPageTemplate');

        $associatedTemplates = [];

        foreach ($this->data['templateIds'] as $templateId){

            /** @var CFixedPageTemplate $fixedPageTemplate */
            $fixedPageTemplate = $fixedPageTemplateRepo->findOneBy(['id'=>$templateId]);

            /** @var CObjectCollection $associatedFixedPages */
            $associatedFixedPages = $fixedPageTemplate->fixedPage;

            if($associatedFixedPages->count() == 0){
                $fixedPageTemplate->delete();
            } else {
                $associatedTemplates[] = $fixedPageTemplate->id;
            }
        }

        return json_encode($associatedTemplates);

    }
}