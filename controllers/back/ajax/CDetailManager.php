<?php
namespace bamboo\controllers\back\ajax;
use bamboo\core\utils\slugify\CSlugify;

/**
 * Class CDetailManager
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date $date
 * @since 1.0
 */
class CDetailManager extends AAjaxController
{
    /**
     * @return string
     */
    public function get()
    {
        $search = $this->app->router->request()->getRequestData()['search'];
        $res = $this->app->dbAdapter->query("SELECT `productDetailId` as `id`, `name` FROM `ProductDetailTranslation` WHERE `langId` = 1 AND `name` like '%" . $search . "%' ORDER BY `name` LIMIT 30", [])->fetchAll();


        foreach ($res as $k => $v) {
            $res[$k]['name'] .= " (";
            $dt = \Monkey::app()->repoFactory->create('ProductDetailTranslation')->findBy(['productDetailId' => $v['id']]);
            $lang = [];
            foreach ($dt as $vt) {
                $rLang = \Monkey::app()->repoFactory->create('Lang')->findOneBy(['id' => $vt->langId]);
                $lang[] = $rLang->lang;
            }
            $res[$k]['name'] .= implode(',', $lang);
            $res[$k]['name'] .= ')';
        }
        return json_encode($res);
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function put()
    {
        $data = $this->app->router->request()->getRequestData();

	    $productDetailId = $data['productDetailId'];
	    $productDetailName = $data['productDetailName'];

	    unset($data['productDetailId']);
	    unset($data['productDetailName']);

        $ids = [];
        \Monkey::app()->repoFactory->beginTransaction();

        foreach ($data as $key => $val) {
	        if($val == $productDetailId) continue;
            if($val == $productDetailName) continue;
            $ids[] = $val;
        }

	    $productDetailPrimary = \Monkey::app()->repoFactory->create("ProductDetail")->findOneBy(['id' => $productDetailId]);
	    $productDetailPrimary->productDetailTranslation->getFirst()->name = $productDetailName;
        $slug = new CSlugify();
        $productDetailPrimary->slug = $slug->slugify($productDetailName);
	    $productDetailPrimary->productDetailTranslation->getFirst()->update();

        $em = $this->app->entityManagerFactory->create('ProductSheetActual');
        try {

            $modelRepo = \Monkey::app()->repoFactory->create('ProductSheetModelActual');
            foreach($ids as $id) {
                if ($id != $productDetailId) {
                    $models = $modelRepo->findBy(['productDetailId' => $id]);
                    foreach($models as $m) {
                        $m->delete();
                    }
                }
            }

            foreach ($ids as $id) {
                $productSheets = $em->findBy(['productDetailId' => $id]);

                foreach ($productSheets as $productSheet) {
                    $productSheet->delete();
                    $productSheet->productDetailId = $productDetailId;
                    $productSheet->insert();
                }
	            $productDetail = \Monkey::app()->repoFactory->create("ProductDetail",false)->findOneBy(['id' => $id]);

	            foreach ($productDetail->productDetailTranslation as $detailTranslation) {
					$detailTranslation->delete();
	            }
	            $productDetail->delete();
            }
            \Monkey::app()->repoFactory->commit();
            return true;
        } catch (\Throwable $e){
            \Monkey::app()->repoFactory->rollback();
	        throw $e;
        }
    }
}