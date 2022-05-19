<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;


/**
 * Class CDictionaryBrandEditController
 * @package bamboo\blueseal\controllers
 *
 * @author Iwes  International Web Ecommerce ServicesTeam <juri@iwes.it>
 *
 * @copyright (c) Iwes International Web Ecommerce Services - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date $date
 * @since 1.0
 */
class CContentTranslationController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "content_translation";

    /**
     * @return string
     */
    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/content_translation.php');
        $langs = \Monkey::app()->repoFactory->create('Lang')->findBy(['isActive'=>1]);
        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'page'=>$this->page,
            'langs'=>$langs,
            'sidebar' => $this->sidebar->build()
        ]);
    }

    /**
     * @return bool
     */
    public function put()
    {
        $request = $this->app->router->request()->getRequestData();
        if(empty($request['string'])) return false;
        $translation = \Monkey::app()->repoFactory->create('Translation')->findOne(['hash'=>$request['hash'],'langId'=>$request['langId']]);
        if(is_null($translation)) {
            $translation = \Monkey::app()->repoFactory->create('Translation')->getEmptyEntity();
            $translation->hash= $request['hash'];
            $translation->langId = $request['langId'];
            $translation->string = $request['string'];
            $translation->insert();
        } else {
            $translation->string = $request['string'];
            $translation->update();
        }
        return true;
    }
}