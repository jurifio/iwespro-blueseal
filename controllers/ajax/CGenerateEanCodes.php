<?php
namespace bamboo\blueseal\controllers\ajax;
use bamboo\core\barcode\CBarCode;
use bamboo\core\barcode\CBarCodeEan13;

/**
 * Class CGenerateEanCodes
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date $date
 * @since 1.0
 */
class CGenerateEanCodes extends AAjaxController
{
    public function post()
    {
    	$start = $this->app->router->request()->getRequestData('start');
	    $end = $this->app->router->request()->getRequestData('end');
	    $counter = 0;
	    for(;$start<$end;$start++) {
	    	$generator = new CBarCodeEan13();
		    $generator->generate($start);
		    try {
			    $this->app->dbAdapter->insert('EanBucket',['ean'=>(string) $generator]);
			    $counter++;
		    } catch (\Exception $e) {

		    }
	    }
	    return $counter;
    }
}