<?php
/**
 * Created by PhpStorm.
 * User: Fabrizio Marconi
 * Date: 20/05/2015
 * Time: 13:00
 */
namespace bamboo\controllers\back\ajax;

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
        $param1 = $this->app->dbAdapter->quote($get);
        $param2 = $this->app->dbAdapter->quote($get . ' !');
        $sql = "SELECT productDetailId FROM `ProductDetailTranslation` WHERE langId = 1 AND (name LIKE " . $param1 . " OR name LIKE " . $param2 . ")";
        $res = $this->app->dbAdapter->query($sql, [])->fetchAll();
        $slug = $this->makeSlugUnique($slug);
        if (!count($res)) {
            try {
                $get .= ' !';
                $this->app->dbAdapter->beginTransaction();
                $newDett = $this->app->repoFactory->create('ProductDetail')->getEmptyEntity();
                $newDett->slug = $slug;
                $retId = $newDett->insert();

                $newTrad = $this->app->repoFactory->create('ProductDetailTranslation')->getEmptyEntity();
                $newTrad->productDetailId = $retId;
                $newTrad->langId = 1;
                $newTrad->name = $get;
                $newTrad->insert();
                $this->app->dbAdapter->commit();
                return "Dettaglio inserito!-" . $retId;
            } catch (\Throwable $e) {
                $this->app->dbAdapter->rollBack();
                return "OOPS! Errore durante l'inserimento, che non è stato eseguito.<br />" . $e->getMessage();
            }
        } else {
            return "Il dettaglio inserito esiste già ed è disponibile";
        }
    }

    public function makeSlugUnique($slug, $recursive = 0) {
        $defSlug = $slug;
        if ($recursive) $defSlug = $slug . '-' . $recursive;
        $det = $this->app->repoFactory->create('ProductDetail')->findOneBy(['slug' => $defSlug]);
        if ($det) {
            return $this->makeSlugUnique($slug, $recursive + 1);
        } else {
            return $defSlug;
        }
    }
}