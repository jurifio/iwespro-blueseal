<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\base\CToken;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;

/**
 * Class CUserListController
 * @package bamboo\blueseal\controllers
 * @author Iwes  International Web Ecommerce ServicesTeam <juri@iwes.it>, ${DATE}
 * @copyright (c) Iwes International Web Ecommerce Services - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */
class CUserManageController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "user_add";

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/user_add.php');
		$sources = [];
	    foreach (\Monkey::app()->repoFactory->create('Marketplace')->findAll() as $marketplace) {
	    	$sources[] = $marketplace->name;
	    }
	    foreach ($this->app->dbAdapter->query("SELECT distinct registrationEntryPoint from User where isDeleted != 1",[])->fetchAll() as $item) {
	        $sources[] = $item['registrationEntryPoint'];
	    }
	    $sources = array_unique($sources);
	    
        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
	        'sources' => $sources,
            'langs' => \Monkey::app()->repoFactory->create('Lang')->findBy(['isActive'=>1]),
            'page' => $this->page,
            'sidebar' => $this->sidebar->build()
        ]);
    }

    public function post()
    {
    	$user = \Monkey::app()->repoFactory->create('User')->getEmptyEntity();
	    $user->email = $this->app->router->request()->getRequestData('user_email');
        if(!empty($this->app->router->request()->getRequestData('user_password'))) {
            $user->password = password_hash($this->app->router->request()->getRequestData('user_password'), PASSWORD_BCRYPT);
        }
	    $user->registrationEntryPoint =  $this->app->router->request()->getRequestData('user_entryPoint');
	    $user->isActive = 1;
	    $user->isDeleted = 0;
        $user->langId = $this->app->router->request()->getRequestData('user_lang') ?? 1;
	    $user->id = $user->insert();

	    $userD = \Monkey::app()->repoFactory->create('UserDetails')->getEmptyEntity();
	    $userD->userId = $user->id;
	    $userD->name = $this->app->router->request()->getRequestData('user_name');
	    $userD->surname = $this->app->router->request()->getRequestData('user_surname');
	    $userD->birthDate = $this->app->router->request()->getRequestData('user_birthdate');
	    $userD->gender = $this->app->router->request()->getRequestData('user_gender');
	    $userD->phone = $this->app->router->request()->getRequestData('user_phone');
	    $userD->fiscalCode = $this->app->router->request()->getRequestData('user_fiscal_code');
        $userD->note = $this->app->router->request()->getRequestData('user_note');
		$userD->insert();

	    $userEmail = \Monkey::app()->repoFactory->create('UserEmail')->getEmptyEntity();
	    $userEmail->userId = $user->id;
	    $userEmail->address = $user->email;
	    $userEmail->isPrimary = true;
	    $userEmail->insert();

	    \Monkey::app()->repoFactory->create('User')->persistRegistrationToken($user->id,(new CToken(64))->getToken(),time() + $this->app->cfg()->fetch('miscellaneous', 'confirmExpiration'));

        if($this->app->router->request()->getRequestData('user_newsletter')) {
            \Monkey::app()->repoFactory->create('NewsletterUser')->insertNewEmail($user->email,$user->id,$user->langId);
        }

	    return $user->id;
    }

    public function put()
    {

        $user = \Monkey::app()->repoFactory->create('User')->findOneByStringId($this->app->router->request()->getRequestData('user_id'));
        $user->email = $this->app->router->request()->getRequestData('user_email');

        if(!empty($this->app->router->request()->getRequestData('user_password'))) {
            $user->password = password_hash($this->app->router->request()->getRequestData('user_password'), PASSWORD_BCRYPT);
        }

        $user->registrationEntryPoint =  $this->app->router->request()->getRequestData('user_entryPoint');
        $user->isActive = 1;
        $user->isDeleted = 0;
        $user->langId = $this->app->router->request()->getRequestData('user_lang') ?? 1;
        $user->update();

        $userD = $user->userDetails;
        $userD->userId = $user->id;
        $userD->name = $this->app->router->request()->getRequestData('user_name');
        $userD->surname = $this->app->router->request()->getRequestData('user_surname');
        $userD->birthDate = $this->app->router->request()->getRequestData('user_birthdate');
        $userD->gender = $this->app->router->request()->getRequestData('user_gender');
        $userD->phone = $this->app->router->request()->getRequestData('user_phone');
        $userD->fiscalCode = $this->app->router->request()->getRequestData('user_fiscal_code');
        $userD->note = $this->app->router->request()->getRequestData('user_note');
        $userD->update();

        if(!$user->newsletterUser && $this->app->router->request()->getRequestData('user_newsletter')) {
            \Monkey::app()->repoFactory->create('NewsletterUser')->insertNewEmail($user->email,$user->id,$user->langId);
        } elseif($user->newsletterUser && !$this->app->router->request()->getRequestData('user_newsletter')) {
            \Monkey::app()->repoFactory->create('NewsletterUser')->unsubscribe($user->email);
        }

        return $user->id;
    }


    public function delete()
    {
        $ids = $this->app->router->request()->getRequestData('users');
        $sql = 'SET FOREIGN_KEY_CHECKS=0';
        \Monkey::app()->dbAdapter->query($sql,[]);
        foreach ($ids as $id) {
            //cancellazione carelli per l'utente
            $cart = \Monkey::app()->repoFactory->create('Cart')->findBy(['userId' => $id]);
            if ($cart != null) {
                foreach ($cart as $carts) {
                    $cartLine = \Monkey::app()->repoFactory->create('CartLine')->findBy(['cartId' => $carts->id]);
                    foreach ($cartLine as $cartLines) {
                        $cartLines->delete();
                    }
                    $userSessionHasCart = \Monkey::app()->repoFactory->create('UserSessionHasCart')->findBy(['cartId' => $carts->id]);
                    foreach ($userSessionHasCart as $userSessionHasCarts) {
                        $userSessionHasCarts->delete();
                    }
                    $carts->delete();
                }
            }

            //cancellazione Ordini per l'utente
            $order = \Monkey::app()->repoFactory->create('Order')->findBy(['userId' => $id]);
            if ($order != null) {
                foreach ($order as $orders) {
                    $this->app->dbAdapter->delete('OrderHistory',['orderId' => $orders->id]);
                    $this->app->dbAdapter->delete('OrderLine',['orderId' => $orders->id]);
                    $this->app->dbAdapter->delete('Order',['id' => $orders->id]);
                }
            }


            $sql = 'DELETE  from Cart where userId=' . $id;
            \Monkey::app()->dbAdapter->query($sql,[]);
            $sql = 'DELETE  FROM Coupon WHERE userId=' . $id;
            \Monkey::app()->dbAdapter->query($sql,[]);
            $sql = 'DELETE  FROM Document WHERE userId=' . $id;
            \Monkey::app()->dbAdapter->query($sql,[]);
            $sql = 'DELETE  FROM EmailAddress WHERE userId=' . $id;
            \Monkey::app()->dbAdapter->query($sql,[]);
            $sql = 'DELETE  FROM Fidelity WHERE memberGetMemberId=' . $id;
            \Monkey::app()->dbAdapter->query($sql,[]);
            $sql = 'DELETE  FROM Fidelity WHERE userId=' . $id;
            \Monkey::app()->dbAdapter->query($sql,[]);
            $sql = 'DELETE  FROM InvoiceSectional WHERE userAddressRecipientId=' . $id;
            \Monkey::app()->dbAdapter->query($sql,[]);
            $sql = 'DELETE  FROM NewsletterUser WHERE userId=' . $id;
            \Monkey::app()->dbAdapter->query($sql,[]);
            $sql = 'DELETE FROM `ProductHistory` WHERE userId=' . $id;
            \Monkey::app()->dbAdapter->query($sql,[]);
            $sql = 'DELETE FROM `StorehouseOperation` WHERE userId=' . $id;
            \Monkey::app()->dbAdapter->query($sql,[]);
            $sql = 'DELETE FROM `Token` WHERE userId=' . $id;
            \Monkey::app()->dbAdapter->query($sql,[]);
            $sql = 'DELETE FROM `UserAddress` WHERE userId=' . $id;
            \Monkey::app()->dbAdapter->query($sql,[]);
            $sql = 'DELETE FROM `UserDetails` WHERE userId=' . $id;
            \Monkey::app()->dbAdapter->query($sql,[]);
            $sql = 'DELETE FROM `UserEmail` WHERE userId=' . $id;
            \Monkey::app()->dbAdapter->query($sql,[]);
            $sql = 'DELETE FROM `UserHasShop` WHERE userId=' . $id;
            \Monkey::app()->dbAdapter->query($sql,[]);
            $sql = 'DELETE FROM `UserOAuth` WHERE userId=' . $id;
            \Monkey::app()->dbAdapter->query($sql,[]);
            $sql = 'DELETE FROM `UserSession` WHERE userId=' . $id;
            \Monkey::app()->dbAdapter->query($sql,[]);
            $sql = 'DELETE FROM `User` WHERE id=' . $id;
            \Monkey::app()->dbAdapter->query($sql,[]);
            $sql = 'SET FOREIGN_KEY_CHECKS=1';
            \Monkey::app()->dbAdapter->query($sql,[]);
            /*$user = \Monkey::app()->repoFactory->create('User')->findOne([$id]);
            $user->isDeleted = 1;
            $user->isActive = 0;
            $user->update();*/
        }

    }
}