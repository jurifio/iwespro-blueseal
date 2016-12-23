<?php
namespace bamboo\blueseal\controllers\ajax;
use bamboo\core\exceptions\BambooThemeException;

/**
 * Class CUserAddressList
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
class CTemplateFetchController extends AAjaxController
{
    public function get()
    {
        $folders = [
            "/back/template/html"
        ];
        $templateName = $this->app->router->request()->getRequestData('templateName');
        foreach ($folders as $folder) {
            $name = $this->app->rootPath().$folder.'/'.$templateName;
            foreach (glob($name.'*') as $file){
                if(is_readable($file)) return file_get_contents($file);
            }
        }

        throw new BambooThemeException('Template File not Found '.$templateName);
    }
}