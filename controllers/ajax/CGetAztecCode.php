<?php
namespace bamboo\controllers\ajax;

use Metzli\Encoder\Encoder;
use Metzli\Renderer\PngRenderer;

/**
 * Class CGetAztecCode
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
class CGetAztecCode extends AAjaxController
{
    public function get()
    {
        $this->app->vendorLibraries->load("aztec");

        $code =  Encoder::encode(base64_decode($_GET['src']));
        $renderer = new PngRenderer();

        header('Content-Type: image/png');
        echo $renderer->render($code);
    }

    public function put()
    {
        $this->get();
    }

    public function post()
    {
        $this->get();
    }

    public function delete()
    {
        $this->get();
    }
}