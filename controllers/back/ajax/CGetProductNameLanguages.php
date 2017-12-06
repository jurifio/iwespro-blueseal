<?php
namespace bamboo\controllers\back\ajax;
use bamboo\core\exceptions\BambooException;

/**
 * Class CBrandDelete
 * @package bamboo\blueseal\controllers\ajax
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */
class CGetProductNameLanguages extends AAjaxController
{
    public function get()
    {
        $names = $this->app->router->request()->getRequestData('name');
        if (!$names) throw new \Exception('non è stato specificato il nome');
        if (is_string($names)) $names = [$names];

        $ret = [];

        foreach($names as $v) {
            $single = [];
            $single['name'] = $v;
            $single['languages'] = [];
            $res = \Monkey::app()->repoFactory->create('ProductName')->findBy(['name' => $v]);
            foreach($res as $v) {
                $single['languages'][] = $v->lang->lang;
            }
            $ret[] = $single;
        }

        return json_encode($ret);
    }
}