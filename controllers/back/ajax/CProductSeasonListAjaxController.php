<?php

namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CProductSeason;
use bamboo\exceptions\BambooEloyException;
use Monkey;
use DateTime;
/**
 * Class CProductSeasonListAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 30/11/2019
 * @since 1.0
 */
class CProductSeasonListAjaxController extends AAjaxController
{
    public function get()
    {
        $datatable = new CDataTables("ProductSeason", ['id'], $_GET, false);
        //$datatable->addCondition('id',\Monkey::app()->repoFactory->create('Shop')->getAutorizedShopsIdForUser());

        $seasons = \Monkey::app()->repoFactory->create('ProductSeason')->findBySql($datatable->getQuery(), $datatable->getParams());
        $count = \Monkey::app()->repoFactory->create('ProductSeason')->em()->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totalCount = \Monkey::app()->repoFactory->create('ProductSeason')->em()->findCountBySql($datatable->getQuery('full'), $datatable->getParams());

        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];
        /** @var CProductSeason $season */
        foreach ($seasons as $season) {
            $row = [];
            $row['DT_RowId'] = $season->printId();
            $row['id'] =  $season->printId();
            $row['name'] = $season->name;
            $row['year'] = $season->year;
            $row['dateStart'] = $season->dateStart;
            $row['dateEnd'] = $season->dateEnd;
            $row['isActive'] = ($season->isActive == 0) ? 'no' : 'si';
            $row['DT_isActive'] = $season->isActive ;
            $row['order'] = $season->order;
            $response['data'][] = $row;
        }
        return json_encode($response);
    }

    public function post()
    {
        $nameValue = \Monkey::app()->router->request()->getRequestData('nameValue');
        $year = \Monkey::app()->router->request()->getRequestData('year');
        $dateStart = \Monkey::app()->router->request()->getRequestData('dateStart');
        $dateEnd = \Monkey::app()->router->request()->getRequestData('dateEnd');
        $isActive=\Monkey::app()->router->request()->getRequestData('isActive');
        $newOrder=\Monkey::app()->dbAdapter->query('SELECT  `ps1`.`order`+1 as newOrder From ProductSeason  ps1 WHERE ps1.id= (SELECT MAX(ps2.id) FROM ProductSeason ps2)',[])->fetchAll()[0]["newOrder"];


        $res = [];
        $res['error'] = 0;
        $res['message'] = 'Nuovo Valore inserito';
        $dba = Monkey::app()->dbAdapter;
        try {
            if (false === $nameValue || false===$year || false===$dateStart || false===$dateEnd ) {
                throw new BambooEloyException('Tutti i campi sono obbligatori');
            }

            \Monkey::app()->repoFactory->beginTransaction();

            $tblRepo = \Monkey::app()->repoFactory->create('ProductSeason');

            $tbl = $tblRepo->getEmptyEntity();
            $tbl->name = $nameValue;
            $tbl->year=(new \DateTime($year))->format('Y');
            $tbl->dateStart=(new \DateTime($dateStart))->format('Y-m-d H:i:s');
            $tbl->dateEnd=(new \DateTime($dateEnd))->format('Y-m-d H:i:s');
            $tbl->isActive=$isActive;
            $tbl->order=$newOrder;

            $tbl->insert();
            \Monkey::app()->repoFactory->commit();

            return json_encode($res);
        } catch
        (BambooEloyException $e) {
            \Monkey::app()->repoFactory->rollback();
            $res['error'] = 1;
            $res['message'] = $e->getMessage();
            return json_encode($res);
        } catch (BambooException $e) {
            \Monkey::app()->repoFactory->rollback();
            \Monkey::app()->router->response()->raiseProcessingError();
            return $e->getMessage();
        }
    }


        /**
         * @return string
         * @transaction
         */
        public
        function put()
        {
            $id = \Monkey::app()->router->request()->getRequestData('id');
            $nameValue = \Monkey::app()->router->request()->getRequestData('nameValue');
            $year = \Monkey::app()->router->request()->getRequestData('year');
            $dateStart = \Monkey::app()->router->request()->getRequestData('dateStart');
            $dateEnd = \Monkey::app()->router->request()->getRequestData('dateEnd');
            $isActive=\Monkey::app()->router->request()->getRequestData('isActive');


            $res = [];
            $res['error'] = 0;
            $res['message'] = 'Stagione Modificata';
            $dba = Monkey::app()->dbAdapter;
            try {
                if (false === $nameValue) {
                    throw new BambooEloyException('Tutti i campi sono obbligatori');
                }

                \Monkey::app()->repoFactory->beginTransaction();

                $tblRepo = \Monkey::app()->repoFactory->create('ProductSeason');

                $tbl = $tblRepo->findOne([$id]);
                if (!$tbl) throw new BambooEloyException('Stagione da aggiornare non trovato');

                $tbl->name = $nameValue;
                $tbl->year=(new \DateTime($year))->format('Y');
                $tbl->dateStart=(new \DateTime($dateStart))->format('Y-m-d H:i:s');
                $tbl->dateEnd=(new \DateTime($dateEnd))->format('Y-m-d H:i:s');
                $tbl->isActive=$isActive;

                $tbl->update();
                Monkey::app()->repoFactory->commit();

                return json_encode($res);
            } catch (BambooEloyException $e) {
                Monkey::app()->repoFactory->rollback();
                $res['error'] = 1;
                $res['message'] = $e->getMessage();
                return json_encode($res);
            } catch (BambooException $e) {
                Monkey::app()->repoFactory->rollback();
                Monkey::app()->router->response()->raiseProcessingError();
                return $e->getMessage();
            }
        }

        /**
         * @throws \Exception
         */
        public
        function delete()
        {
            try {
                $id = Monkey::app()->router->request()->getRequestData('id');
                $res = [];
                $res['error'] = 0;
                $res['message'] = 'Valore Cancellato';

                $sql = 'DELETE  from ProductSeason where id=' . $id;
                Monkey::app()->dbAdapter->query($sql, []);

                return json_encode($res);


            } catch (\Throwable $e) {
                return $res = $e;
            }


        }

    }