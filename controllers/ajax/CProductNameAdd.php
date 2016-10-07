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
class CProductNameAdd extends AAjaxController
{
    public function get(){
        $name = trim(\Monkey::app()->router->request()->getRequestData('name'));
        return $this->isName($name);
    }

    /**
     * @return bool
     */
    public function post()
    {
        $name = trim($this->app->router->request()->getRequestData()['name']);
        $exists = $this->isName($name);

        if ("ok" == $exists) {
            try {
                $slug = "lea sso roscio";
                $this->app->dbAdapter->beginTransaction();
                $newName = $this->app->repoFactory->create('ProductDetail')->getEmptyEntity();
                $newName->slug = $slug;
                $retId = $newName->insert();

                $newTrad = $this->app->repoFactory->create('ProductDetailTranslation')->getEmptyEntity();
                $newTrad->productDetailId = $retId;
                $newTrad->langId = 1;
                $newTrad->name = $name;
                $newTrad->insert();
                $this->app->dbAdapter->commit();
                return "Dettaglio inserito!-" . $retId;
            } catch (\Exception $e) {
                $this->app->dbAdapter->rollBack();
                return "OOPS! Errore durante l'inserimento, che non è stato eseguito.<br />" . $e->getMessage();
            }
        } else {
            return "Il dettaglio inserito esiste già ed è disponibile";
        }
    }

    public function isName($name){
        if ('' == $name) return "ok";
        $last2 = substr($name, -2);
        if (' !' == $last2) {
            $param1 = $this->app->dbAdapter->quote(substr($name, 0, -2));
            $param2 = $this->app->dbAdapter->quote($name);
        } else {
            $param1 = $this->app->dbAdapter->quote($name);
            $param2 = $this->app->dbAdapter->quote($name . ' !');
        }
        $sql = "SELECT count(*) as count FROM `ProductNameTranslation` WHERE langId = 1 AND (name LIKE " . $param1 . " OR name LIKE " . $param2 . ")";
        $res = $this->app->dbAdapter->query($sql, [])->fetchAll()[0];
        if ($res['count']) return "ko";
        else return "ok";
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