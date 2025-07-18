<?php

namespace bamboo\blueseal\controllers;

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;
use bamboo\core\db\pandaorm\repositories\CRepo;

/**
 * Class CEditorialPlanAddController
 * @package bamboo\blueseal\controllers
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 19/12/2017
 * @since 1.0
 */
class CEditorialPlanAddController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "editorialplan_add";

    /**
     * @return string
     * @throws \bamboo\core\exceptions\RedPandaInvalidArgumentException
     */

    public function get()
    {
        $view = new VBase(array());

        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths', 'blueseal') . '/template/editorialplan_add.php');
        $contracts=\Monkey::app()->repoFactory->create('Contracts')->findAll();
        $foisonRepo=\Monkey::app()->repoFactory->create('Foison');
        foreach ($contracts as $contract) {
            $foison=$foisonRepo->findOneBy(['id'=>$contract->foisonId]);
            $operator=$foison->name.' '.$foison->surname;
            $collectContract[] = ['id' => $contract->id,'name' => $contract->name,'operator' => $operator];
        }

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'page'=>$this->page,
            'collectContract'=>$collectContract,
            'sidebar' => $this->sidebar->build()
        ]);

    }
}