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
        $currentUser = \Monkey::app()->getUser();
        $currentUserId = $currentUser->id;


        /** @var CRepo $wishListRepo */
        $wishListRepo = \Monkey::app()->repoFactory->create('WishList');

        /** @var CWishListCheckItem $wishListCheckItem*/
        $wishListCheckItem = $wishListRepo->findOneBy(['id' => $product,'userId'=>$currentUserId]);
        $wishListCheckItem->delete();

            $response = "Cancellazione Eseguita";


        return $response;
    }
}