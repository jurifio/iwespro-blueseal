<?php
namespace bamboo\blueseal\controllers\ajax;
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
class CGetDummyPicture extends AAjaxController
{
    public function get()
    {
        $code = $this->app->router->request()->getRequestData('code');
        if (!$code) throw new BambooException('OOPS! Non è stato fornito il codice prodotto!');

        $p = \Monkey::app()->repoFactory->create('Product')->findOneByStringId($code);
        if (!$p) return json_encode(['status' => 'ko', 'message' => 'Il prodotto non esiste nel catalogo']);

        $img = $p->getDummyPictureUrl();
        return json_encode(['status' => 'ok', 'dummyPic' => $img]);
    }
}