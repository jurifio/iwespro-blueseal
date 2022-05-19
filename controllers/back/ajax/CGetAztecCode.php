<?php
namespace bamboo\controllers\back\ajax;

use Metzli\Encoder\Encoder;
use Metzli\Renderer\PngRenderer;

/**
 * Class CGetAztecCode
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
class CGetAztecCode extends AAjaxController
{
    public function get()
    {
        $this->app->vendorLibraries->load("aztec");
        $input = $_GET['src'];
        if(preg_match("/^[0-9]+-[0-9]+/u",$input) == 0) {
            $input = base64_decode($_GET['src']);
        }
        $code =  Encoder::encode($input);
        $renderer = new PngRenderer();

        header('Content-Type: image/png');
        return $renderer->render($code);
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