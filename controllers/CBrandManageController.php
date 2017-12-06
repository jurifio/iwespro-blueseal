<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\exceptions\BambooException;
use bamboo\core\exceptions\RedPandaAssetException;
use bamboo\core\utils\amazonPhotoManager\ImageManager;
use bamboo\core\utils\amazonPhotoManager\S3Manager;
use bamboo\core\utils\slugify\CSlugify;
use bamboo\utils\file\SFileToolbox;

/**
 * Class CBrandManageController
 * @package bamboo\blueseal\controllers
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date $date
 * @since 1.0
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
	    $brandRepo = \Monkey::app()->repoFactory->create('ProductBrand');
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
            $productBrand = \Monkey::app()->repoFactory->create("ProductBrand")->findOneBy(['id' => $data['ProductBrand_id']]);
            $productBrand->slug = trim($slug);
            $productBrand->name = trim($data['ProductBrand_name']);
            $productBrand->description = $data['ProductBrand_description'];
            $productBrand->update();

            if(!empty($data['ProductBrand_logo']) && (strpos($data['ProductBrand_logo'],'amazonaws') === false)) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $data['ProductBrand_logo']);
                curl_setopt($ch, CURLOPT_HEADER, FALSE);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
                $imgBody = curl_exec($ch);
                curl_close($ch);
                if ($imgBody != false && !empty($imgBody)) {
                    $this->app->vendorLibraries->load("amazon2723");
                    $config = $this->app->cfg()->fetch('miscellaneous', 'amazonConfiguration');
                    $tempFolder = $this->app->rootPath().$this->app->cfg()->fetch('paths', 'tempFolder')."/";
                    $extension = pathinfo($data['ProductBrand_logo'],PATHINFO_EXTENSION);
                    $putRes = file_put_contents($tempFolder . $productBrand->slug, $imgBody);

                    if(!$extension || empty($extension) || strlen($extension) > 4) {
                        $extension = SFileToolbox::getExtensionFromFileMimeType($tempFolder . $productBrand->slug);
                    }


                    $manager = new S3Manager($config['credential']);
                    $res = $manager->putImage('iwes',$tempFolder . $productBrand->slug,'logos',$productBrand->slug.'.'.$extension);
                    $productBrand->logoUrl = $res->get('ObjectURL');;
                    $productBrand->update();
                }
            }
            return json_encode($productBrand);
        } catch (\Throwable $e) {
            $this->app->router->response()->raiseUnauthorized();
            return false;
        }
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
        }catch (\Throwable $e) {
            header("Location: ".$blueseal."/prodotti/brand/modifica?productBrandId=".$data['ProductBrand_id']);
        }
    }
}