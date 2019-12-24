<?php

namespace bamboo\blueseal\controllers;

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;
use bamboo\core\db\pandaorm\repositories\CRepo;

/**
 * Class CEmailTemplateAddController
 * @package bamboo\blueseal\controllers
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 24/12/2019
 * @since 1.0
 */
class CEmailTemplateAddController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "emailtemplate_add";

    /**
     * @return string
     * @throws \bamboo\core\exceptions\RedPandaInvalidArgumentException
     */

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths', 'blueseal') . '/template/emailtemplate_add.php');
        $langs=\Monkey::app()->repoFactory->create('Lang')->findBy(['isActive'=>1]);
        $languages=[];
        $i=0;
        $larray=[];
        foreach($langs as $lang){
            $lg=['id'=>$lang->id,'lang'=>$lang->lang,'name'=>$lang->name];
            array_push($languages,$lg);
            $i++;
            array_push($larray,$lang->lang);
        }
        $arrayl=implode('-',$larray);


        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'page'=>$this->page,
            'languages'=>$languages,
            'arrayl'=>$arrayl,
            'sidebar' => $this->sidebar->build()
        ]);

    }
}