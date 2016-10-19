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
        $res = $this->isName($name);
        return ($res) ? 'ko' : 'ok';
    }

    /**
     * @return bool
     */
    public function post()
    {
        $name = trim($this->app->router->request()->getRequestData()['name']);
        $exists = $this->isName($name);

        if (!$exists) {
            $pntRepo = \Monkey::app()->repoFactory->create('ProductNameTranslation');
            try {
                $pntRepo->insertName($name);
                return "ok";
            } catch (\Throwable $e) {
                $this->app->dbAdapter->rollBack();
                return "OOPS! Errore durante l'inserimento, che non è stato eseguito.<br />" . $e->getMessage();
            }
        } else {
            return "Il dettaglio inserito esiste già ed è disponibile";
        }
    }

    public function isName($name){
        $pntRepo = \Monkey::app()->repoFactory->create('ProductNameTranslation');
        return $pn = $pntRepo->findByName($name);
    }
}