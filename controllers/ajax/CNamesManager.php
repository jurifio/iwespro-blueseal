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
        $res = $this->app->dbAdapter->query(
            "UPDATE ProductNameTranslation SET `name` = TRIM(CONCAT(UCASE(LEFT(`name`, 1)),  SUBSTRING(LCASE(`name`), 2)))",
            []
        )->countAffectedRows();

        $this->app->cacheService->getCache('entities')->flush();
        return $res . " dei prodotti sono stati normalizzati";
    }

    private function mergeNames($new, $old)
    {
        $pntRepo = $this->app->repoFactory->create('ProductNameTranslation');
        foreach ($old as $old1) {
            $productNameTranslation = $pntRepo->findBy(['name' => $old1]);
            foreach($productNameTranslation as $pntRow) {
                if ($pntRow->langId != 1) {
                    $pntRow->delete();
                } else {
                    $pntRow->name = trim($new);
                    $pntRow->update();
                }
            }
        }
        return "Nomi aggiornati!";
        /* vecchia logica
        $SQLCond = str_repeat('`name` = ? OR ', count($old));
        $SQLCond = rtrim($SQLCond, 'OR ');
        $cond = array_merge([$new], $old);
        try {
            $this->dbAdapter->query('DELETE ProductNameTranslation WHERE langId <> 1 AND (' . $SQLCond . ')', $old);
            $this->app->dbAdapter->query('UPDATE ProductNameTranslation SET `name` = ? WHERE langId = 1 AND (' . $SQLCond . ')', $cond);
        } catch (\Exception $e) {
            return 'OOPS! C\'è stato un problema!';
        }
        return 'Nomi aggiornati!';
        */
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
        } catch (\Exception $e) {
            $this->app->dbAdapter->rollBack();
            return 'OOPS! C\'è stato un problema!';
        }
        /*
        vecchia logica

        $SQLCond = str_repeat('`productVariantId` = ? OR ', count($old));
        $SQLCond = rtrim($SQLCond, 'OR ');
        $cond = array_merge([$new], $old);

        try {
            $this->app->dbAdapter->beginTransaction();
            $this->app->dbAdapter->query('DELETE FROM ProductNameTranslation WHERE langId <> 1 AND (' . $SQLCond . ')', $old);
            $this->app->dbAdapter->query('UPDATE ProductNameTranslation SET `name` = ? WHERE langId = 1 AND (' . $SQLCond . ')', $cond);
            $this->app->dbAdapter->commit();
        } catch (\Exception $e) {
            $this->app->dbAdapter->rollBack();
            return 'OOPS! C\'è stato un problema!';
        }
        return 'Nomi aggiornati!';
         */
    }

    /**
     * @return string
     */
    public function get()
    {
        $get = $this->app->router->request()->getRequestData();
        $codes = [];
        $searchByCodes = '';
        $searchByNames = '';
        $concat = '';

        foreach ($get as $k => $v) {
            if (false === strpos($k, 'codes_')) continue;
            $codes[] = explode('-', $v)[1];
        }
        if (count($codes)) {
            $questionMarks = rtrim(str_repeat('?,', count($codes)), ',');
            $searchByCodes = ' `productVariantId` in (' . $questionMarks . ') ';
        }

        $search = (array_key_exists('search', $get)) ? $get['search'] : '';
        if ($search) {
            $searchByNames = ' `name` like ? ';
            $codes[] = '%' . $search . '%';
        }

        if ($search && (1 < count($codes))) {
            $concat = ' OR ';
        }

        $where = $searchByCodes . $concat . $searchByNames;

        //$repo = $this->app->repoFactory->create('ProductDetailTranslation',false);
        $res = $this->app->dbAdapter->query("SELECT distinct `name` FROM `ProductNameTranslation` WHERE `langId` = 1 AND ( $where ) ORDER BY `name` LIMIT 30", $codes)->fetchAll();

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