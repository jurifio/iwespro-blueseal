<?php
/**
 * Created by PhpStorm.
 * User: Fabrizio Marconi
 * Date: 20/05/2015
 * Time: 13:00
 */
namespace bamboo\blueseal\controllers\ajax;

use bamboo\core\utils\slugify\CSlugify;

/**
 * Class CGetAutocompleteData
 * @package bamboo\app\controllers
 */
class CProductDetailAddNewAjaxController extends AAjaxController
{
    /**
     * @return bool
     */
    public function post()
    {
        $get = trim($this->app->router->request()->getRequestData()['name']);
        $last2 = substr($get, -2);
        $slugify = new CSlugify();
        $slug = $slugify->slugify($get);
        $get = ( ' !' == $last2) ? substr($get, 0, -2) : $get;
        $sql = "SELECT productDetailId FROM `ProductDetailTranslation` WHERE langId = 1 AND (name LIKE '" . $get . "' OR name LIKE '" . $get . " !')";
        $res = $this->app->dbAdapter->query($sql, [])->fetchAll();
        if (!count($res)) {
            try {
                $get .= ' !';
                $this->app->dbAdapter->beginTransaction();
                $retId = $this->app->dbAdapter->insert('ProductDetail', ['slug' => $slug]);
                $retTrad = $this->app->dbAdapter->insert('ProductDetailTranslation',['productDetailId' => $retId, 'langId' => 1, 'name' => $get]);
                $this->app->dbAdapter->commit();
                return "Dettaglio inserito!-" . $retId;
            } catch (\Exception $e) {
                return "OOPS! Errore durante l'inserimento, che non è stato eseguito.<br />" . $e->getMessage();
            }
        } else {
            return "Il dettaglio inserito esiste già ed è disponibile";
        }
    }
}