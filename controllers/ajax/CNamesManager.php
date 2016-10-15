<?php
namespace bamboo\blueseal\controllers\ajax;

use bamboo\core\exceptions\BambooException;
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
            switch ($get['action']) {
                case "clean":
                    $res = $this->cleanNames();
                    break;
                case "merge":
                    $res = $this->mergeNames($get['newName'], $get['oldNames']);
                    break;
                case "mergeByProducts":
                    $res = $this->mergeByProducts($get);
                    break;
                default:
                    return "OOPS! Non so cosa devo fare. Contatta un amministratore";
            }

            return $res;
        }
    }

    private function cleanNames()
    {
        $resPNT = \Monkey::app()->dbAdapter->query(
            "UPDATE ProductNameTranslation SET `name` = TRIM(CONCAT(UCASE(LEFT(`name`, 1)),  SUBSTRING(LCASE(`name`), 2)))",
            []
        )->countAffectedRows();

        $resPN = \Monkey::app()->dbAdapter->query(
            "UPDATE ProductName SET `name` = TRIM(CONCAT(UCASE(LEFT(`name`, 1)),  SUBSTRING(LCASE(`name`), 2))), `translation` = TRIM(CONCAT(UCASE(LEFT(`translation`, 1)),  SUBSTRING(LCASE(`translation`), 2)))",
            []
        )->countAffectedRows();

        $this->app->cacheService->getCache('entities')->flush();
        return $resPNT . " dei nomi assegnati ai prodotti e " . $resPN . " dei nomi sono stati normalizzati.";
    }

    private function mergeNames($new, $old)
    {
        $pnRepo = \Monkey::app()->repoFactory->create('ProductName');
        $pntRepo = $this->app->repoFactory->create('ProductNameTranslation');
        try {
            \Monkey::app()->dbAdapter->beginTransaction();
            foreach ($old as $o) {

                $productNames = $pnRepo->findBy(['name' => $o]);
                foreach($productNames as $pnRow) {
                    $pnRow->delete();
                }

                $productNameTranslation = $pntRepo->findBy(['name' => $o]);
                foreach ($productNameTranslation as $pntRow) {
                    if ($pntRow->langId != 1) {
                        $pntRow->delete();
                    } else {
                        $pntRow->name = trim($new);
                        $pntRow->update();
                    }
                }
            }
        } catch (\Exception $e) {
            \Monkey::app()->dbAdapter->rollBack();
            throw new \Exception($e->getMessage());
        }
        return "Nomi aggiornati!";
    }

    private function mergeByProducts($get)
    {
        $new = $get['newName'];
        $oldCodes = $get['oldCodes'];
        $old = [];
        try {
            $this->app->dbAdapter->beginTransaction();
            foreach ($oldCodes as $v) {
                $product = $this->app->repoFactory->create('Product', false)->findOneByStringId($v);
                foreach ($product->productNameTranslation as $productNameTranslation) {
                    if ($productNameTranslation->langId = 1) {
                        $productNameTranslation->name = ucfirst(trim($new));
                        $productNameTranslation->update();
                    } else {
                        $productNameTranslation->delete();
                    }
                }
                $old[] = explode('-', $v)[1];
            }
            $this->app->dbAdapter->commit();
            return 'Nomi aggiornati!';
        } catch (\Throwable $e) {
            $this->app->dbAdapter->rollBack();
            return 'OOPS! C\'è stato un problema!';
        }
    }

    /**
     * @return string
     */
    public function get()
    {
        $search = $this->app->router->request()->getRequestData('search');
        $codes = [];

        if (false === $search) throw new BambooException('Non è stata specificata una stringa di ricerca');
        $searchByNames = ' `name` like ? ';
        $codes[] = '%' . $search . '%';


        $res = $this->app->dbAdapter->query("SELECT DISTINCT `name` FROM `ProductName` WHERE `langId` = 1 AND ( $searchByNames ) ORDER BY `name` LIMIT 30", $codes)->fetchAll();

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