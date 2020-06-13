<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\domain\entities\CProductHasProductCorrelation;


/**
 * Class CProductCorrelationAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 13/06/2020
 * @since 1.0
 */
class CProductCorrelationAjaxController extends AAjaxController
{
    public function post()
    {
        $data = \Monkey::app()->router->request()->getRequestData();
        $productCorrelationRepo=\Monkey::app()->repoFactory->create('ProductCorrelation');
        $name=$data['name'];
        if ($name==null){
            return 'Nome non Valorizzato';
        }
        $description=$data['description'];
        $note=$data['note'];
        $findpc=$productCorrelationRepo->findOneBy(['name'=>$name]);
        if($findpc!=null){
            return 'Esiste Già una correlazione con questo nome';
        }
        $pc=$productCorrelationRepo->getEmptyEntity();
        $pc->name=$name;
        $pc->description=$description;
        $pc->note=$note;
        $pc->insert();
        return 'Correlazione inserita con successo';

    }
    public function put()
    {
        $data = \Monkey::app()->router->request()->getRequestData();
        $productCorrelationRepo=\Monkey::app()->repoFactory->create('ProductCorrelation');
        $id=$data['id'];
        $name=$data['name'];
        if ($name==null){
            return 'Nome non Valorizzato';
        }
        $description=$data['description'];
        $note=$data['note'];
        $pc=$productCorrelationRepo->findOneBy(['id'=>$id]);
        $pc->name=$name;
        $pc->description=$description;
        $pc->note=$note;
        $pc->update();
        return 'Correlazione Aggiornata con successo';
    }
    public function delete()
    {
        $data = \Monkey::app()->router->request()->getRequestData();
        /** @var CRepo $productCorrelationRepo */
        $productCorrelationRepo=\Monkey::app()->repoFactory->create('ProductCorrelation');
        /** @var CRepo $productHasProductCorrelationRepo  */
        $productHasProductCorrelationRepo=\Monkey::app()->repoFactory->create('ProductHasProductCorrelation');
        $id=$data['id'];
        /** @var CProductHasProductCorrelation $phpc */
        $phpc=$productHasProductCorrelationRepo->findBy(['correlationId'=>$id]);
        foreach ($phpc as $values){
            $values->delete();
        }
        /** @var CProductCorrelation $pc */
        $pc=$productCorrelationRepo->findOneBy(['id'=>$id]);
        $pc->delete();
        return 'Correlazioni  Cancellate con successo';
    }
}