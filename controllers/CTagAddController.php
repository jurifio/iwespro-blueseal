<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;

/**
 * Class CCouponAddController
 * @package redpanda\app\controllers
 */
class CTagAddController extends ARestrictedAccessRootController
{
    /**
     * @var string
     */
    protected $fallBack = "blueseal";
    /**
     * @var string
     */
    protected $pageSlug = "tag_add";

    /**
     * @throws \redpanda\core\exceptions\RedPandaORMException
     */
    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->cfg()->fetch('paths','blueseal').'/template/tag_add.php');

        $em = $this->app->entityManagerFactory->create('Tag');

        $tagEdit = null;
        if (isset($_GET['tagId'])) {
            $tagEdit = $em->findOne([$_GET['tagId']]);
        }

        $sortings = $this->app->repoFactory->create('SortingPriority')->findAll();

        echo $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'sorting' => $sortings,
            'tagEdit' => $tagEdit,
            'page'=>$this->page,
            'sidebar'=>$this->sidebar->build()
        ]);
    }
}