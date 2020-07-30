<?php

namespace bamboo\blueseal\controllers;

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;

/**
 * Class CProductModelRevertListSupportController
 * @package bamboo\blueseal\controllers
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 29/07/2020
 * @since 1.0
 */
class CProductModelRevertListSupportController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "product_modelrevert_list_support";


    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths', 'blueseal') . '/template/product_modelrevert_list_support.php');
        $detailLabel=\Monkey::app()->repoFactory->create('ProductDetailLabelTranslation')->findBy(['langId'=>1]);
        if(isset($_GET['detaillabelid'])) {
            $detailLabelId=$_GET['detaillabelid'];
        }else{
            $detailLabelId='notSet';
        }
        if(isset($_GET['selectdefine'])) {
            $selectDefine=$_GET['selectdefine'];
        }else{
            $selectDefine='';
        }
        if(isset($_GET['textdefine'])) {
            $textDefine=$_GET['textdefine'];
        }else{
            $textDefine='';
        }
        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'page'=>$this->page,
            'detailLabelId'=>$detailLabelId,
            'detailLabel'=>$detailLabel,
            'selectDefine'=>$selectDefine,
            'textDefine'=>$textDefine,
            'sidebar' => $this->sidebar->build()
        ]);
    }
}