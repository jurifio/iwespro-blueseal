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

            $newPn = $pnRepo->findBy(['name' => $new]);
            if (!$newPn->count()) {
                $newPn = $pnRepo->getEmptyEntity();
                $newPn->name = $new;
                $newPn->langId = 1;
                $newPn->translation = $new;
                $newPn->insert();
            }

            foreach ($old as $o) {
                $productNames = $pnRepo->findBy(['name' => $o]);
                foreach($productNames as $pnRow) {
                    $pnRow->delete();
                }
            }

            $pntRepo->updateTranslationFromName($new, $old);

            \Monkey::app()->dbAdapter->commit();
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
            $pntRepo = \Monkey::app()->repoFactory->create('ProductNameTranslation');
            foreach ($oldCodes as $v) {
                list($id, $productVariantId) = explode(',', $v);
                $pntRepo->updateProductName($id, $productVariantId, $new);
            }
            $this->app->dbAdapter->commit();
            return 'Nomi aggiornati!';
        } catch (\Throwable $e) {
            $this->app->dbAdapter->rollBack();
            return 'OOPS! C\'è stato un problema!<br />' . $e->getMessage();
        }
    }

    /**
     * @return string
     */
    public function get()
    {
        $search = $this->app->router->request()->getRequestData('search');
        $codes = [];
        $res = [];
        if (false !== $search) {
            $searchByNames = ' `name` like ? ';
            $codes[] = '%' . $search . '%';

            $res = $this->app->dbAdapter->query("SELECT DISTINCT `name` FROM `ProductName` WHERE `langId` = 1 AND ( $searchByNames ) ORDER BY `name` LIMIT 30", $codes)->fetchAll();

            $pntRepo = \Monkey::app()->repoFactory->create('ProductNameTranslation');

            foreach($res as $k => $v) {
                $pn = $pntRepo->findByName($v['name']);
                $res[$k]['languages'] = [];
                foreach($pn as $pnsingle) {
                    $res[$k]['languages'][] = $pnsingle->lang->lang;
                }
            }
        }
        return json_encode($res);
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function put()
    {
        $this->get();
    }


}