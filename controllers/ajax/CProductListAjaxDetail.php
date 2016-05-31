<?php
namespace bamboo\blueseal\controllers\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;

/**
 * Class CProductListAjaxController
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>, ${DATE}
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
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

        $modifica = $this->urls['base']."prodotti/modifica";
        
        $idDetail = $this->app->router->request()->getRequestData();
        foreach ($idDetail as $k => $idD) {
            $idDetail[$k] = "psa.productDetailId = " . $idD;
        }
        $where = implode(" AND ", $idDetail);

        $pId = $this->app->dbAdapter->query(
            "SELECT 
                  psa.productId as productId,
                  psa.productVariantId as productVariantId 
                  FROM ProductSheetActual psa, ProductDetail pd  
                  WHERE pd.id = psa.productDetailId AND " . $where,
            []
        )->fetchAll();
        $productList = "";
        foreach($pId as $v) {
            $cats = $this->app->categoryManager->getCategoriesForProduct($v['productId'], $v['productVariantId']);
            $catsName = [];
            foreach($cats as $catv) {
                $catName = $this->app->dbAdapter->select('ProductCategoryTranslation', ['productCategoryId' => $catv['id'], 'langId' => 1])->fetchAll();
                if (isset($catName[0])) $catsName[] = $catName[0]['name'];
            }
            $cats = implode( " - " , $catsName);
            
            $productList .= '<a style="width: 120px" href="' . $modifica . "?id=" . $v['productId'] . '&productVariantId=' . $v['productVariantId'] . '">' . $v['productId'] . '-' . $v['productVariantId'] . '</a><span style="width: 250px;"> cat: '. $cats  .'</span><br />';
        }

        return $productList;
    }

    /**
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    public function delete(){

        //$sql ="SELECT pd.id as id FROM ProductDetail as pd LEFT JOIN ProductSheetActual as psa WHERE pd.id = psa.productDetailId AND psa.productDetailId IS NULL";

        $error = false;
        try {
            $this->app->dbAdapter->beginTransaction();
            
            $resPDA = $this->app->dbAdapter->query("DELETE pda FROM ProductSheetActual pda, (SELECT pd.id
                                         FROM `ProductDetail` `pd`
                                           JOIN `ProductSheetActual` `psa`
                                           JOIN `ProductSku` `ps`
                                         WHERE ((`pd`.`id` = `psa`.`productDetailId`) AND
                                                (`psa`.`productId` = `ps`.`productId`) AND
                                                (`psa`.`productVariantId` = `ps`.`productVariantId`) AND
                                                (`pd`.`slug` <> ''))
                                         GROUP BY `psa`.`productDetailId`
                                         HAVING (sum(`ps`.`stockQty`) = 0)) q1
WHERE pda.productDetailId = q1.id", [] )->countAffectedRows();

            $resDT = $this->app->dbAdapter->query("Delete pdt
                      FROM ProductDetailTranslation pdt join `ProductDetail` `pd` on (pdt.productDetailId = pd.id) LEFT JOIN
                      `ProductSheetActual` `psa` on (pd.id = psa.productDetailId)
                      WHERE psa.productDetailId is null", [])->countAffectedRows();

            $resD = $this->app->dbAdapter->query("Delete pd FROM `ProductDetail` `pd` LEFT JOIN `ProductSheetActual` `psa` on (pd.id = psa.productDetailId) WHERE psa.productDetailId is null", [])->countAffectedRows();

        } catch (\Exception $e) {
            $this->app->dbAdapter->rollBack();
            $error = true;
        }

        $this->app->dbAdapter->commit();
        if ($error) return "OOPS! C'è stato un errore nella cancellazione dei dettagli!<br />Niente è andato perduto. Contatta l'amministratore.";
        $message = $resD . " dettagli non associati a nessun prodotto sono stati cancellati, insieme alle relative " . $resDT . " traduzioni";
        return $message;
    }
}