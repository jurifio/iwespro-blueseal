<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\db\pandaorm\entities\IEntity;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\core\utils\slugify\CSlugify;
use bamboo\domain\entities\CPostCategory;
use bamboo\ecommerce\views\VBase;

/**
 * Class CBlogPostCategoryController
 * @package bamboo\app\controllers
 */
class CBlogPostCategoryController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "blog_category";

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/blog_category.php');



        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'page'=>$this->page,
            'sidebar' => $this->sidebar->build(),
	        'rootCats' => \Monkey::app()->repoFactory->create('PostCategory')->findBy(['parentPostCategoryId'=>null]),
	        'cats' => \Monkey::app()->repoFactory->create('PostCategory')->findAll()
        ]);
    }

	public function post()
	{
		$data = $this->app->router->request()->getRequestData();
		$category = \Monkey::app()->repoFactory->create('PostCategory')->getEmptyEntity();
		$s = new CSlugify();
		$category->slug = $s->slugify($data['PostCategoryTranslation.name']);
		if (isset($data['PostCategory.parentPostCategoryId']) && !empty($data['PostCategory.parentPostCategoryId'])) {
			$category->parentPostCategoryId = $data['PostCategory.parentPostCategoryId'];
		}
		$category->id = $category->insert();

		$categoryTranslation = \Monkey::app()->repoFactory->create('PostCategoryTranslation')->getEmptyEntity();
		$categoryTranslation->postCategoryId = $category->id;
		$categoryTranslation->langId = $this->app->getLang()->getId();
		$categoryTranslation->name = $data['PostCategoryTranslation.name'];
		$categoryTranslation->insert();
		return true;
	}

	/**
	 * @return bool
	 * @throws \Exception
	 */
	public function delete()
	{
		$data = $this->app->router->request()->getRequestData('ids');
		$ids = explode(',',$data);

		try{
			\Monkey::app()->repoFactory->beginTransaction();
			foreach ($ids as $id) {
				$postCategory = \Monkey::app()->repoFactory->create('PostCategory')->findOneBy(['id'=>$id]);
				$this->recursiveDelete($postCategory);
			}
			\Monkey::app()->repoFactory->commit();
		} catch (\Throwable $e) {
			\Monkey::app()->repoFactory->rollback();
			throw $e;
		}

		return true;
	}

	/**
	 * @param IEntity|null $postCategory
	 */
	private function recursiveDelete($postCategory) {
		if(!is_null($postCategory)) {
			foreach ($postCategory->childrenPostCategory as $item) {
				$this->recursiveDelete($item);
			}
			foreach ($postCategory->postCategoryTranslation as $item){
				$item->delete();
			}
			$postCategory->delete();
		}
	}
}