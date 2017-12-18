<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\exceptions\BambooException;
use bamboo\core\utils\slugify\CSlugify;
use bamboo\domain\entities\CProductSize;
use bamboo\domain\repositories\CProductSizeRepo;


/**
 * Class CProductSizeGroupManage
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date $date
 * @since 1.0
 */
class CSizeFullListManage extends AAjaxController
{
    /**
     * @return int
     * @throws BambooException
     * @throws \Exception
     */
    public function post()
    {
        $data = \Monkey::app()->router->request()->getRequestData();
        $slug = $data['slug'];
        $name = $data['name'];

        //controllo se i campi inseriti in input siano compilati
        if(empty($slug) || empty($name)){
            $res = "Inserire sia lo slug che il nome della taglia prima di inserire";
            return $res;
        } else {

            //Slugghifica una string (es. toglie spazi vuoti)
            $s = new CSlugify();
            $slug = $s->slugify($slug);

            /** @var CProductSize $productSizeRepo */
            $productSizeRepo = \Monkey::app()->repoFactory->create('ProductSize')->getEmptyEntity();


            // Controllo se esiste una taglia per i valori in input
            /** @var CProductSizeRepo $checkProductSizeRepo */
            $checkProductSizeRepo = \Monkey::app()->repoFactory->create('ProductSize');

            /** @var CProductSizeRepo $checkSlug */
            $checkSlug = $checkProductSizeRepo->findOneBy(['slug' => $slug]);

            /** @var CProductSizeRepo $checkName */
            $checkName = $checkProductSizeRepo->findOneBy(['name' => $name]);

            if (empty($checkSlug) && empty($checkName)) {
                //riempi il database
                $productSizeRepo->slug = $data['slug'];
                $productSizeRepo->name = $data['name'];
                $productSizeRepo->smartInsert();
                //restituisci messaggio di avvenuto inserimento
                $res = "INSERIMENTO DELLA TAGLIA AVVENUTO CON SUCCESSO";
            } else {
                //err. 500 http
                //\Monkey::app()->router->response()->raiseProcessingError();
                //restituisci messaggio di errore
                $res = "LA TAGLIA CHE HAI INSERITO ESISTE GIA'";
            }
            return $res;
        }
    }

}