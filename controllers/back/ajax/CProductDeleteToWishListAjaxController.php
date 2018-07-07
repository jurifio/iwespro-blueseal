<?php
/**
 * Created by PhpStorm.
 * User: Fabrizio Marconi
 * Date: 20/05/2015
 * Time: 13:00
 */
namespace bamboo\controllers\back\ajax;

use bamboo\core\utils\slugify\CSlugify;
use bamboo\domain\entities\CWishList;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CShop;
use bamboo\domain\entities\CUser;
use bamboo\core\base\CObjectCollection;

/**
 * Class CGetAutocompleteData
 * @package bamboo\app\controllers
 */
class CProductDeleteToWishListAjaxController extends AAjaxController
{
    /**
     * @return bool
     */
    public function post()
    {
        //prendo i dati passati in input
        $data = \Monkey::app()->router->request()->getRequestData();
        $product =$data['product'];
        $status  =$data['status'];
        $currentUser = \Monkey::app()->getUser();
        $currentUserId = $currentUser->id;


        /** @var CRepo $wishListRepo */
        $wishListRepo = \Monkey::app()->repoFactory->create('WishList');

        /** @var CWishListCheckItem $wishListCheckItem*/
        $wishListCheckItem = $wishListRepo->findOneBy(['id' => $product,'userId'=>$currentUserId]);
        $wishListCheckItem->statusId=$status;
        $now = date("Y-m-d H:i:s");
        $wishListCheckItem->deleteDate = $now;
        $wishListCheckItem->Update();

            $response = "Cancellazione Eseguita";


        return $response;
    }
}