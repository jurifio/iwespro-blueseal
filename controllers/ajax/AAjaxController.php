<?php
namespace bamboo\blueseal\controllers\ajax;

use bamboo\core\application\AApplication;

/**
 * Class AAjaxController
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>, ${DATE}
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */
class AAjaxController
{
    /** @var AApplication $app */
    protected $app;
    /** @var array $data */
    protected $data;

    public function __construct(AApplication $app)
    {
        $this->app = $app;
        $this->data = $this->app->router->request()->getRequestData();
    }

    public function rfc($name) {
        return \Monkey::app()->repoFactory->create($name);
    }

    /**
     * @param $action
     * @return string
     */
    public function createAction($action)
    {
        return $this->{$action}();
    }

}