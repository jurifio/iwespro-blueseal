<?php
namespace bamboo\blueseal\controllers\ajax;

/**
 * Class CUserAddress
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 22/07/2016
 * @since 1.0
 */
class CUserAddress extends AAjaxController
{
	public function post(){
		var_dump($this->app->router->request()->getRequestData());
	}
}