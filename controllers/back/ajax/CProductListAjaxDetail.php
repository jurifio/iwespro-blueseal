<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;

/**
 * Class CProductListAjaxController
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Iwes  International Web Ecommerce ServicesTeam <juri@iwes.it>, ${DATE}
 *
 * @copyright (c) Iwes International Web Ecommerce Services - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */
class CProductListAjaxDetail extends AAjaxController
{
    protected $urls = [];
    protected $authorizedShops = [];
    protected $em;

    /**
     * @param $action
     * @return mixed
     */
    public function createAction($action)
    {
        $this->app->setLang(new CLang(1,'it'));
        $this->urls['base'] = $this->app->baseUrl(false)."/blueseal/";
        $this->urls['page'] = $this->urls['base']."prodotti";
        $this->urls['dummy'] = $this->app->cfg()->fetch('paths','dummyUrl');

        if ($this->app->getUser()->hasPermission('allShops')) {

        } else{
            $res = $this->app->dbAdapter->select('UserHasShop',['userId'=>$this->app->getUser()->getId()])->fetchAll();
            foreach($res as $val) {
                $this->authorizedShops[] = $val['shopId'];
            }
        }

        $this->em = new \stdClass();
        $this->em->products = $this->app->entityManagerFactory->create('Product');

        return $this->{$action}();
    }

    public function get()
    {

        $modifica = $this->urls['base']."friend/prodotti/modifica";
        
        $idDetail = $this->app->router->request()->getRequestData();
        foreach ($idDetail as $k => $idD) {
            $idDetail[$k] = "psa.productDetailId = " . $idD;
        }
        
        $idDetailCond = "(";
        
        $idDetailCond .= implode(" OR ", $idDetail);
        
        $idDetailCond .= ")";
        
        
        $where = $idDetailCond ;//. " AND ps.code in ('A', 'P', 'I')";

        $pId = $this->app->dbAdapter->query(
            "SELECT 
                  psa.productId as productId,
                  psa.productVariantId as productVariantId,
                  ps.name as `status`
                  FROM ProductSheetActual psa, ProductDetail pd, Product p, ProductStatus ps  
                  WHERE pd.id = psa.productDetailId AND psa.productVariantId = p.productVariantId AND ps.id = p.productStatusId AND (" . $where . ")",
            []
        )->fetchAll();
        $productList = "";
        $homeShop = \Monkey::app()->cfg()->fetch("general","shop-id");
        foreach($pId as $v) {
            $cats = $this->app->categoryManager->getCategoriesForProduct($v['productId'], $v['productVariantId']);
            $catsName = [];
            foreach($cats as $catv) {
                $catName = $this->app->dbAdapter->select('ProductCategoryTranslation', ['productCategoryId' => $catv['id'], 'langId' => 1,'shopId'=>44])->fetchAll();
                if (isset($catName[0])) $catsName[] = $catName[0]['name'];
            }
            $cats = implode( " - " , $catsName);
            
            $productList .= '<a style="width: 120px" href="' . $modifica . "?id=" . $v['productId'] . '&productVariantId=' . $v['productVariantId'] . '">' . $v['productId'] . '-' . $v['productVariantId'] . '</a> (' . $v['status'] . ') <span style="width: 250px;"> cat: '. $cats  .'</span><br />';
        }
        return $productList;
    }

    /**
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    public function delete(){

        //$sql ="SELECT pd.id as id FROM ProductDetail as pd LEFT JOIN ProductSheetActual as psa WHERE pd.id = psa.productDetailId AND psa.productDetailId IS NULL";
        $idDetail = $this->app->router->request()->getRequestData();
        $error = false;
        \Monkey::app()->repoFactory->beginTransaction();

        if (count($idDetail)) {
            $ids = [];
            foreach ($idDetail as $idD) {
                $ids[] = $idD;
            }

            $in = implode(", ", $ids);
            $error = false;
            try {
                $resPDA = $this->app->dbAdapter->query("DELETE FROM ProductSheetActual WHERE productDetailId IN (" . $in . ")", [])->countAffectedRows();
            } catch (\PDOException $e) {
                \Monkey::app()->repoFactory->rollback();
                $error = true;
                $pdoErr = $e->errorInfo;
            }
            try {
                $resPDA = $this->app->dbAdapter->query("DELETE FROM ProductSheetModelActual WHERE productDetailId IN (" . $in . ")", [])->countAffectedRows();
            } catch (\PDOException $e) {
                \Monkey::app()->repoFactory->rollback();
                $error = true;
                $pdoErr = $e->errorInfo;
            }
            try {
                $resDT = $this->app->dbAdapter->query("DELETE FROM ProductDetailTranslation WHERE productDetailId IN (" . $in . ")", [])->countAffectedRows();
            } catch (\PDOException $e) {
                \Monkey::app()->repoFactory->rollback();
                $error = true;
                $pdoErr = $e->errorInfo;
            }
            try {
                $resD = $this->app->dbAdapter->query("DELETE FROM `ProductDetail` WHERE id IN (" . $in . ")", [])->countAffectedRows();
            } catch (\PDOException $e) {
                \Monkey::app()->repoFactory->rollback();
                $error = true;
                $pdoErr = $e->errorInfo;
            }
            $message = "I dettagli selezionati sono stati cancellati";
        } else {

            try {
                $resPDA = $this->app->dbAdapter->query("DELETE pda FROM ProductSheetActual pda, (SELECT pd.id as id
                    FROM (((`ProductDetail` `pd`
                      JOIN `ProductSheetActual` `psa`)
                      JOIN `ProductSku` `ps` ON (`psa`.`productId` = `ps`.`productId`) AND (`psa`.`productVariantId` = `ps`.`productVariantId`))
                      LEFT JOIN `ProductSheetModelActual` `psma` ON `psma`.`productDetailId` = `pd`.`id`)
                    WHERE ((`pd`.`id` = `psa`.`productDetailId`) AND
                           (`pd`.`slug` <> ''))
                          AND `psma`.`productDetailId` is NULL
                    GROUP BY `psa`.`productDetailId` HAVING sum(`ps`.`stockQty`) = 0) q1
                WHERE pda.productDetailId = q1.id", [])->countAffectedRows();

                $resDT = $this->app->dbAdapter->query("DELETE pdt
                      FROM ProductDetailTranslation pdt JOIN `ProductDetail` `pd` ON (pdt.productDetailId = pd.id) LEFT JOIN
                      `ProductSheetActual` `psa` ON (pd.id = psa.productDetailId) LEFT JOIN 
                      `ProductSheetModelActual` `psma` ON (pd.id = psma.productDetailId)
                      WHERE psa.productDetailId IS NULL AND psma.productDetailId IS NULL", [])->countAffectedRows();

                $resD = $this->app->dbAdapter->query(
                    "DELETE pd FROM `ProductDetail` `pd` 
                      LEFT JOIN `ProductSheetActual` `psa` ON (pd.id = psa.productDetailId)
                      LEFT JOIN `ProductSheetModelActual` `psma` ON (pd.id = psma.productDetailId)
                      WHERE psa.productDetailId IS NULL AND psma.productDetailId IS NULL",
                    [])->countAffectedRows();
            } catch (\PDOException $e) {
                \Monkey::app()->repoFactory->rollback();
                $error = true;
                $PDOError = $e->errorInfo;
            }
            $message = $resD . " dettagli non associati a nessun prodotto sono stati cancellati, insieme alle relative " . $resDT . " traduzioni";
        }

        \Monkey::app()->repoFactory->commit();
        // NON TOGLIERE "OOPS" DAL MESSAGGIO D'ERRORE. Viene cercato nel js che chiama sto metodo
        if ($error) return "OOPS! C'è stato un errore nella cancellazione dei dettagli!<br />Niente è andato perduto. Contatta l'amministratore.<br />"
            . "$PDOError<br />";
        return $message;
    }
}