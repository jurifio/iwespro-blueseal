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
class CProductLookAjaxController extends AAjaxController
{
    public function post()
    {
        $data = \Monkey::app()->router->request()->getRequestData();
        $productLookRepo=\Monkey::app()->repoFactory->create('ProductLook');
        $name=$data['name'];
        $images=$data['image'];
        $discountActive=$data['discountActive'];
        $typeDiscount=$data['typeDiscount'];
        $amount=$data['amount'];
        if ($name==null){
            return 'Nome non Valorizzato';
        }
        $description=$data['description'];
        $note=$data['note'];
        $findpc=$productLookRepo->findOneBy(['name'=>$name]);
        if($findpc!=null){
            return 'Esiste Già una Look con questo nome';
        }
        $pc=$productLookRepo->getEmptyEntity();
        $i=0;
        foreach($images as $image){
            if($i==0) {
                $pc->image = $image;
            }elseif($i==1){
                $pc->image2 = $image;
            }elseif($i==2){
                $pc->image3 = $image;
            }elseif($i==4){
                $pc->image4 = $image;
            }else{
                break;
            }

            $i++;
        }
        $pc->name=$name;
        $pc->discountActive=$discountActive;
        $pc->typeDiscount=$typeDiscount;
        $pc->amount=$amount;
        $pc->description=$description;
        $pc->note=$note;
        $pc->image=$image;
        $pc->insert();
        return 'Look inserito con successo';

    }
    public function put()
    {
        $data = \Monkey::app()->router->request()->getRequestData();
        $productLookRepo=\Monkey::app()->repoFactory->create('ProductLook');
        $id=$data['id'];
        $name=$data['name'];
        $discountActive=$data['discountActive'];
        $typeDiscount=$data['typeDiscount'];
        $amount=$data['amount'];
        if ($name==null){
            return 'Nome non Valorizzato';
        }
        $description=$data['description'];
        $note=$data['note'];
        $pc=$productLookRepo->findOneBy(['id'=>$id]);
        $pc->name=$name;
        $pc->description=$description;
        $pc->note=$note;
        $pc->discountActive=$discountActive;
        $pc->typeDiscount=$typeDiscount;
        $pc->amount=$amount;
        $pc->update();
        return 'Look Aggiornato con successo';
    }
    public function delete()
    {
        $data = \Monkey::app()->router->request()->getRequestData();
        /** @var CRepo $productLookRepo */
        $productLookRepo=\Monkey::app()->repoFactory->create('ProductLook');
        /** @var CRepo $productHasProductLookRepo  */
        $productHasProductLookRepo=\Monkey::app()->repoFactory->create('ProductHasProductLook');
        $id=$data['id'];
        /** @var CProductHasProductLook $phpc */
        $phpc=$productHasProductLookRepo->findBy(['productLookId'=>$id]);
        if($phpc) {
            foreach ($phpc as $values) {
                $values->delete();
            }
        }
        /** @var CProductLook $pc */
        $pc=$productLookRepo->findOneBy(['id'=>$id]);
        $pc->delete();
        return 'Look  Cancellate con successo';
    }
}