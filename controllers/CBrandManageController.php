<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\exceptions\RedPandaAssetException;
use bamboo\core\utils\amazonPhotoManager\ImageManager;
use bamboo\core\utils\amazonPhotoManager\S3Manager;
use bamboo\core\utils\slugify\CSlugify;

/**
 * Class CBrandManageController
 * @package bamboo\app\controllers
 */
class CBrandManageController extends ARestrictedAccessRootController
{
    /**
     * @var string
     */
    protected $fallBack = "blueseal";
    /**
     * @var string
     */
    protected $pageSlug = "product_brand_add";

    /**
     *
     */
    public function put()
    {
        $data = $this->app->router->request()->getRequestData();
        $slugify = new CSlugify();

        if (isset($data['ProductBrand_slug']) && !empty(trim($data['ProductBrand_slug']))) {
            $slug = $slugify->slugify($data['ProductBrand_slug']);
        } else {
            $slug = $slugify->slugify($data['ProductBrand_name']);
        }
	    $brandRepo = $this->app->repoFactory->create('ProductBrand');
	    $brand = $brandRepo->findOne([$data['ProductBrand_id']]);

	    //TODO FINISH DEVELOPING RENAME BRAND
	    $TESTING = true;
	    if(!$TESTING && $brand->slug != $slug ){
		    $this->app->vendorLibraries->load("amazon2723");
		    $config = $this->app->cfg()->fetch('miscellaneous', 'amazonConfiguration');
		    $image = new ImageManager(new S3Manager($config['credential']), $this->app, "");
		    if(!$image->copy($brand->slug.'/',$config['bucket'],$slug.'/',$config['bucket'])) {
			    throw new RedPandaAssetException('Error moving files');
		    };
	    }

        try {

            $productBrand = $this->app->repoFactory->create("ProductBrand")->findOneBy(['id' => $data['ProductBrand_id']]);
            $productBrand->slug = trim($slug);
            $productBrand->name = trim($data['ProductBrand_name']);
            $this->app->repoFactory->create("ProductBrand")->update($productBrand);

        } catch (\Exception $e) {
            $this->app->router->response()->raiseUnauthorized();
        }
      //  $this->app->dbAdapter->update("ProductBrand",["slug"=>trim($slug), "name"=>trim($data['ProductBrand_name'])],["id"=>$data['ProductBrand_id']]);
    }

    /**
     *
     */
    public function post()
    {
        $data = $this->app->router->request()->getRequestData();
        $blueseal = $this->app->baseUrl(false).'/blueseal';
        $slugify = new CSlugify();

        if (isset($data['ProductBrand_slug']) && !empty(trim($data['ProductBrand_slug']))) {
            $slug = $slugify->slugify($data['ProductBrand_slug']);
        } else {
            $slug = $slugify->slugify($data['ProductBrand_name']);
        }

        $this->app->dbAdapter->insert("ProductBrand", ["slug"=>trim($slug), "name"=>trim($data['ProductBrand_name'])]);

        if(!headers_sent()){
            header("Location: ".$blueseal."/prodotti/brand");
        }
    }

    /**
     *
     */
    public function delete()
    {
        $data = $this->app->router->request()->getRequestData();
        $blueseal = $this->app->baseUrl(false).'/blueseal';

        try{
            $this->app->dbAdapter->delete('ProductBrand',array('id'=>$data['ProductBrand_id']));
        }catch (\Exception $e) {
            header("Location: ".$blueseal."/prodotti/brand/modifica?productBrandId=".$data['ProductBrand_id']);
        }
    }
}