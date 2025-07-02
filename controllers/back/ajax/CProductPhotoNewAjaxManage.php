<?php
namespace bamboo\controllers\back\ajax;

use bamboo\core\exceptions\RedPandaAssetException;
use bamboo\core\exceptions\RedPandaException;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\core\utils\amazonPhotoManager\ImageManager;
use bamboo\core\utils\amazonPhotoManager\S3Manager;
use bamboo\domain\entities\CProduct;
use bamboo\ecommerce\views\VBase;
use bamboo\core\utils\upload\ProductImageUploader;

/**
 * Class CProductPhotoAjaxManage
 * @package bamboo\blueseal\controllers\ajax
 * @author Iwes  International Web Ecommerce ServicesTeam <juri@iwes.it>, ${DATE}
 * @copyright (c) Iwes International Web Ecommerce Services - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */
class CProductPhotoNewAjaxManage extends AAjaxController
{
    public function get()
    {
        $imgSize = 562;

        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths', 'blueseal') . '/template/widgets/photos.php');
        $id = $this->app->router->request()->getRequestData('id');
        $productVariantId = $this->app->router->request()->getRequestData('productVariantId');
        $product = \Monkey::app()->repoFactory->create('Product')->findOne([$id, $productVariantId]);
if($product) {
    $photos = $this->app->dbAdapter->query("SELECT distinct `name`, `order`,size
                                      FROM ProductPhoto pp, ProductHasProductPhoto ppp
                                      WHERE pp.id = ppp.productPhotoId AND
                                      ppp.productId = ? AND
                                      ppp.productVariantId = ? AND
                                      pp.size = ?
                                      GROUP BY pp.id
                                      ORDER BY pp.order",[$product->id,$product->productVariantId,$imgSize])->fetchAll();
}else{
    $photos=null;
}


        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'photos' => $photos,
            'product' => $product
        ]);
    }

    public function post()
    {
        /** @var $product CProduct */
        $product = \Monkey::app()->repoFactory->create('Product')->findOneBy(['id'=>$this->app->router->request()->getRequestData('id'),
            'productVariantId'=>$this->app->router->request()->getRequestData('productVariantId')]);
        $this->app->vendorLibraries->load("amazon2723");
        $config = $this->app->cfg()->fetch('miscellaneous', 'amazonConfiguration');
        $tempFolder = $this->app->rootPath().$this->app->cfg()->fetch('paths', 'tempFolder')."/";
        $productFolder=$this->app->rootPath().$this->app->cfg()->fetch('paths', 'ProductFolder')."/";

        $image = new ImageManager(new S3Manager($config['credential']), $this->app, $tempFolder);

        if (!move_uploaded_file($_FILES['file']['tmp_name'], $tempFolder . $_FILES['file']['name'])) {
            throw new RedPandaException('Cannot move the uploaded Files named '.$_FILES['file']['tmp_name'].' in ' .$tempFolder.$_FILES['file']['name']);
        }


        $fileName['name'] = $product->printId();
        $fileName['number'] = (string) str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);
        $fileName['extension'] = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);

        try{
            $res = $image->processLocal($_FILES['file']['name'], $fileName, $productFolder);
        }catch( RedPandaAssetException $e){
            $this->app->router->response()->raiseProcessingError();
            return 'Dimensioni della foto errate: il rapporto deve esser 9:16';
        }

        $futureDummy = "";
        if (count($res) < 4) {
            //error
        } else {
            foreach ($res as $key => $val) {
                if (empty($futureDummy)) {
                    $futureDummy = $val;
                }
                $orderMax=1;
                $sql="SELECT MAX(`pp`.`order`) + 1 AS orderMax FROM ProductPhoto pp  JOIN ProductHasProductPhoto php ON pp.id =php.productPhotoId JOIN Product p ON php.productId=p.id AND php.productVariantId=p.productVariantId
WHERE p.id=".$product->id." AND p.productVariantId=".$product->productVariantId;
                $res=$this->app->dbAdapter->query($sql,[])->fetchAll();
                foreach($res as $result){
                    $orderMax=$result['orderMax'];
                }
                if($orderMax==null){
                    $orderMax=1;
                }
                $ids[] = $this->app->dbAdapter->insert('ProductPhoto', array('name' => $val, 'order' => $orderMax, 'mime'=>'image/jpeg', 'size' => $key, 'isPublic'=>1,'local'=>1));
            }
            unlink($tempFolder . $_FILES['file']['name']);
            $count = 0;
            foreach ($ids as $key => $val) {
                $this->app->dbAdapter->insert("ProductHasProductPhoto", ["productId" => $product->id, "productVariantId" => $product->productVariantId, "productPhotoId" => $val]);
                $count++;
            }
            if ($count) {
                \Monkey::app()->eventManager->triggerEvent(
                    'assignPhotosToProduct',
                    [
                        'product' => $product,
                        'photoIds' => $ids,
                        'release' => 'release'
                    ]
                );
            }
        }

        return true;
    }

    public function put()
    {
        $data = $this->app->router->request()->getRequestData();
        $product = \Monkey::app()->repoFactory->create('Product')->findOne([$data['id'], $data['productVariantId']]);
        unset($data['id']);
        unset($data['productVariantId']);

        \Monkey::app()->repoFactory->beginTransaction();
        foreach ($product->productPhoto as $photo) {
            foreach ($data as $newOrder => $oldOrder) {
                $oldOrder = (int)substr($oldOrder, 5);
                $newOrder = ((int)substr($newOrder, 1)) + 1;
                if ($oldOrder != $photo->order || $oldOrder == $newOrder ) continue;
                $photo->order = $newOrder;
                $photo->update();
                break;
            }
        }
        \Monkey::app()->repoFactory->commit();
        return true;
    }

    public function delete()
    {
        $id = $this->app->router->request()->getRequestData('id');
        $productVariantId = $this->app->router->request()->getRequestData('productVariantId');

        $product = \Monkey::app()->repoFactory->create('Product')->findOne([$id,$productVariantId]);

        $res = $this->app->dbAdapter->query("SELECT pp.id, pp.name
                                      FROM  ProductHasProductPhoto ppp,
                                            ProductPhoto pp
                                      WHERE ppp.productPhotoId = pp.id AND
                                            ppp.productId = ? AND
                                            ppp.productVariantId = ? AND
                                            pp.`order` = ?",[
            $id,
            $productVariantId,
            substr($this->app->router->request()->getRequestData('photoOrder'),5)])->fetchAll();
        \Monkey::app()->repoFactory->beginTransaction();

        $this->app->vendorLibraries->load("amazon2723");
        $config = $this->app->cfg()->fetch('miscellaneous', 'amazonConfiguration');
        $s3 = new S3Manager($config['credential']);

        foreach($res as $photo){
            if ($photo['local']==null) {
                $del = $s3->delImage($product->productBrand->slug . "/" . $photo['name'], $config['bucket']);
            }else{
                if(ENV=='dev'){
                    unlink('/media/sf_sites/iwespro/client/public/product/'.$photo['name']);
                    $del=1;
                }else{
                    unlink('/home/iwespro/client/public/product/'.$photo['name']);
                    $del=1;
                }
            }
            if(!$del) {
                \Monkey::app()->repoFactory->rollback();
                throw new RedPandaException('Could not Delete all the photos');
            }
            $this->app->dbAdapter->delete('ProductHasProductPhoto',["productId"=>$id,"productVariantId"=>$productVariantId,"productPhotoId"=>$photo['id']]);
            $this->app->dbAdapter->delete('ProductHasProductPhoto',["productId"=>$id,"productVariantId"=>$productVariantId,"productPhotoId"=>$photo['id']]);
            $this->app->dbAdapter->delete('ProductPhoto',["id"=>$photo['id']]);
        }
        \Monkey::app()->repoFactory->commit();
    }

}