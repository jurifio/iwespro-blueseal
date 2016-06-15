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
        $last2 = substr($get, strlen($get)-2);
        $get = ( ' !' !== $last2) ? $get . ' !' : $get;
        $slugify = new CSlugify();
        $slug = $slugify->slugify($get);
        $res = $this->app->dbAdapter->select('ProductDetail', ['slug' => $slug])->fetchAll();
        if (!count($res)) {
            try {
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