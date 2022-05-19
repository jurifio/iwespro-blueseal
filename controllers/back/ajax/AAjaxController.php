<?php
namespace bamboo\controllers\back\ajax;

use bamboo\core\application\AApplication;

/**
 * Class AAjaxController
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Iwes  International Web Ecommerce ServicesTeam <juri@iwes.it>, ${DATE}
 *
 * @copyright (c) Iwes International Web Ecommerce Services - All rights reserved
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