<?php
namespace bamboo\blueseal\controllers\ajax;

/**
 * Class CChangeUserPassword
 * @package bamboo\blueseal\controllers\ajax
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
class CChangeUserPassword extends AAjaxController
{
    /**
     * @return string
     */
    public function get() {
        return bin2hex(mcrypt_create_iv(6, MCRYPT_DEV_URANDOM));
    }

    public function put()
    {
        $uId = $this->app->router->request()->getRequestData('userId');
        $user = $this->app->repoFactory->create('User')->findOneByStringId($uId);
        $pwd = $this->app->router->request()->getRequestData('password');
        $user->password  = password_hash($pwd,PASSWORD_BCRYPT);
        $user->update();
        return $pwd;
    }
}