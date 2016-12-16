<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\db\pandaorm\adapter\CMySQLAdapter;
use bamboo\core\exceptions\BambooException;
use bamboo\core\utils\slugify\CSlugify;

/**
 * Class CNameTranslateManageController
 * @package redpanda\blueseal\controllers
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
class CNameTranslateManageController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";

    public function put()
    {
        $idIta = \Monkey::app()->router->request()->getRequestData('italianNameId');
        if (!$idIta) throw new BambooException('L\'id del nome in lingua italiana è obbligatorio');

        $data = $this->app->router->request()->getRequestData();


        $pnRepo = \Monkey::app()->repoFactory->create('ProductName');
        $pntRepo = \Monkey::app()->repoFactory->create('ProductNameTranslation');
        $langRepo = \Monkey::app()->repoFactory->create('Lang');
        $langItaId = $langRepo->findOneBy(['lang' => 'it'])->id;

        foreach ($data as $key => $val) {
            if ('italianNameId' == $key) continue;
            $keys = explode('_', $key);
            if('' == $val) {
                if ($langItaId == $keys[1]) throw new BambooException('Il nome prodotto in italiano è obbligatorio!');
            }
                $transNames[$keys[1]] = trim($val);
        }

        $pn = $pnRepo->findOneBy(['id' => $idIta]);
        if (!$pn) throw new BambooException('Non si può modificare la traduzione di un nome inesistente');

        $this->app->dbAdapter->beginTransaction();
        try {
            $pn = $pnRepo->findBy(['name' => $pn->name]);
            foreach($pn as $n) {
                $n->name = trim($transNames[$langItaId]);
                if ($langItaId == $n->langId) $n->translation = $transNames[$langItaId];
                $n->update();
            }

            foreach($transNames as $k => $v){
                $pn = $pnRepo->findOneBy(['name' => $transNames[$langItaId], 'langId' => $k]);
                if ('' != $v) {
                    if ($pn) {
                        $pn->translation = trim($v);
                        $pn->update();
                    } else {
                        $pn = $pnRepo->getEmptyEntity();
                        $pn->name = $transNames[$langItaId];
                        $pn->langId = $k;
                        $pn->translation = trim($v);
                        $pn->insert();
                    }
                } elseif ($pn) {
                    $pn->delete();
                }
            }
            $query = "SELECT productId, productVariantId FROM `ProductNameTranslation` WHERE name = ? GROUP BY productVariantId";
            $res = \Monkey::app()->dbAdapter->query($query, [$transNames[$langItaId]])->fetchAll();
            foreach($res as $k => $v){
                foreach($transNames as $l => $name) {
                $pnt = $pntRepo->findOneBy(['productId' => $v['productId'], 'productVariantId' => $v['productVariantId'], 'langId' => $l]);
                    if ('' != $name) {
                        if ($pnt) {
                            $pnt->name = trim($name);
                            $pnt->update();
                        } else {
                            $pnt= $pntRepo->getEmptyEntity();
                            $pnt->productId = $v['productId'];
                            $pnt->productVariantId = $v['productVariantId'];
                            $pnt->langId = $l;
                            $pnt->name = trim($name);
                            $pnt->insert();
                        }
                    } elseif ($pnt) {
                        $pnt->delete();
                    }
                }
            }
            $this->app->dbAdapter->commit();
            return true;
        } catch (\Throwable $e) {
            $this->app->dbAdapter->rollBack();
            throw new \Exception($e->getMessage());
        }
    }

}