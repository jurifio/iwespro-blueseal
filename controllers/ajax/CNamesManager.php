<?php
namespace bamboo\blueseal\controllers\ajax;
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
class CNamesManager extends AAjaxController
{

    public function post()
    {
        $get = $this->app->router->request()->getRequestData();

        if (isset($get['action'])) {
            switch($get['action']) {
                case "clean":
                    $res = $this->cleanNames();
                    break;
                case "merge":
                    $res = $this->mergeNames($get['newName'], $get['oldNames']);
                    break;
                default:
                    return "OOPS! Non so cosa devo fare. Contatta un amministratore";
            }
            return $res;
        }
    }

    private function cleanNames() {
        $res = $this->app->dbAdapter->query(
            "UPDATE ProductNameTranslation SET `name` = TRIM(CONCAT(UCASE(LEFT(`name`, 1)),  SUBSTRING(LCASE(`name`), 2)))",
            []
        )->countAffectedRows();

        return $res . " dei prodotti sono stati normalizzati";
    }

    private function mergeNames($new, $old){
        $SQLCond = str_repeat('`name` = ? OR ', count($old));
        $SQLCond = rtrim($SQLCond, 'OR ');
        $cond = array_merge([$new], $old);

        $this->app->dbAdapter->query('UPDATE ProductNameTranslation SET `name` = ? WHERE langId = 1 AND (' . $SQLCond . ')', $cond);
        return 'Nomi aggiornati!';
    }

    /**
     * @return string
     */
    public function get()
    {
       $search = $this->app->router->request()->getRequestData()['search'];
        //$repo = $this->app->repoFactory->create('ProductDetailTranslation',false);
        $res = $this->app->dbAdapter->query("SELECT `name` FROM `ProductNameTranslation` WHERE `langId` = 1 AND `name` like '%" . $search . "%' ORDER BY `name` LIMIT 30", [])->fetchAll();

        return json_encode($res);
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function put()
    {
     /*   $data = $this->app->router->request()->getRequestData();

	    $productDetailId = $data['productDetailId'];
	    $productDetailName = $data['productDetailName'];

	    unset($data['productDetailId']);
	    unset($data['productDetailName']);

        $ids = [];
        $this->app->dbAdapter->beginTransaction();

        foreach ($data as $key => $val) {
	        if($val == $productDetailId) continue;
            if($val == $productDetailName) continue;
            $ids[] = $val;
        }

	    $productDetailPrimary = $this->app->repoFactory->create("ProductDetail")->findOneBy(['id' => $productDetailId]);
	    $productDetailPrimary->productDetailTranslation->getFirst()->name = $productDetailName;
        $slug = new CSlugify();
        $productDetailPrimary->slug = $slug->slugify($productDetailName);
	    $productDetailPrimary->productDetailTranslation->getFirst()->update();

        $em = $this->app->entityManagerFactory->create('ProductSheetActual');
        try {
            foreach ($ids as $id) {
                $productSheets = $em->findBy(['productDetailId' => $id]);

                foreach ($productSheets as $productSheet) {
                    $productSheet->delete();
                    $productSheet->productDetailId = $productDetailId;
                    $productSheet->insert();
                }
	            $productDetail = $this->app->repoFactory->create("ProductDetail",false)->findOneBy(['id' => $id]);

	            foreach ($productDetail->productDetailTranslation as $detailTranslation) {
					$detailTranslation->delete();
	            }
	            $productDetail->delete();
            }
            $this->app->dbAdapter->commit();
            return true;
        } catch (\Exception $e){
            $this->app->dbAdapter->rollBack();
	        throw $e;
        }*/
    }


}