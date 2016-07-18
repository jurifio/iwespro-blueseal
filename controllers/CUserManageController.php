<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\base\CToken;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;

/**
 * Class CUserListController
 * @package bamboo\blueseal\controllers
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>, ${DATE}
 * @copyright (c) Bambooshoot snc - All rights reserved
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

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'page' => $this->page,
            'sidebar' => $this->sidebar->build()
        ]);
    }

    public function post()
    {
    	$user = $this->app->repoFactory->create('User')->getEmptyEntity();
	    $user->email = $this->app->router->request()->getRequestData('user_email');
	    $user->password = password_hash($this->app->router->request()->getRequestData('user_password'), PASSWORD_BCRYPT);
	    $user->registrationEntryPoint = 'backoffice';
	    $user->isActive = 1;
	    $user->isDeleted = 0;
	    $user->id = $user->insert();

	    $userD = $this->app->repoFactory->create('UserDetails')->getEmptyEntity();
	    $userD->userId = $user->id;
	    $userD->name = $this->app->router->request()->getRequestData('user_name');
	    $userD->surname = $this->app->router->request()->getRequestData('user_surname');
	    $userD->birthDate = $this->app->router->request()->getRequestData('user_birthdate');
	    $userD->gender = $this->app->router->request()->getRequestData('user_gender');
	    $userD->phone = $this->app->router->request()->getRequestData('user_phone');
	    $userD->fiscalCode = $this->app->router->request()->getRequestData('user_fiscal_code');
		$userD->insert();

	    $userEmail = $this->app->repoFactory->create('UserEmail')->getEmptyEntity();
	    $userEmail->userId = $user->id;
	    $userEmail->address = $user->email;
	    $userEmail->isPrimary = true;
	    $userEmail->insert();

	    $this->app->repoFactory->create('User')->persistRegistrationToken($user->id,(new CToken(64))->getToken(),time() + $this->app->cfg()->fetch('miscellaneous', 'confirmExpiration'));

	    return $user->id;
    }
}