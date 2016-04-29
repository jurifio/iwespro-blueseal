<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\core\utils\slugify\CSlugify;
use bamboo\ecommerce\views\VBase;

/**
 * Class CBlogPostTagController
 * @package bamboo\app\controllers
 */
class CBlogPostTagController extends ARestrictedAccessRootController
{
	protected $fallBack = "blueseal";
	protected $pageSlug = "blog_tag";

	public function get()
	{
		$view = new VBase([]);
		$view->setTemplatePath($this->app->rootPath() . $this->app->cfg()->fetch('paths', 'blueseal') . '/template/blog_tag.php');

		return $view->render([
			'app'     => new CRestrictedAccessWidgetHelper($this->app),
			'page'    => $this->page,
			'sidebar' => $this->sidebar->build(),
			'tags'    => $this->app->repoFactory->create('PostTag')->findAll()
		]);
	}

	public function post()
	{
		$data = $this->app->router->request()->getRequestData();
		$tag = $this->app->repoFactory->create('PostTag')->getEmptyEntity();
		$s = new CSlugify();
		$tag->slug = $s->slugify($data['PostTagTranslation.name']);
		$tag->id = $tag->insert();

		$tagTranslation = $this->app->repoFactory->create('PostTagTranslation')->getEmptyEntity();
		$tagTranslation->postTagId = $tag->id;
		$tagTranslation->langId = $this->app->getLang()->getId();
		$tagTranslation->name = $data['PostTagTranslation.name'];
		$tagTranslation->insert();

		return true;
	}

	/**
	 * @return bool
	 * @throws \Exception
	 */
	public function delete()
	{
		$data = $this->app->router->request()->getRequestData('ids');
		$ids = explode(',', $data);

		foreach ($ids as $id) {
			try {
				$postTag = $this->app->repoFactory->create('PostTag')->findOneBy(['id' => $id]);;
				foreach ($postTag->postTagTranslation as $item) {
					$item->delete();
				}
				$postTag->delete();
				$this->app->dbAdapter->commit();
			} catch (\Exception $e) {
				$this->app->dbAdapter->rollBack();
				throw $e;
			}
		}

		return true;
	}
}