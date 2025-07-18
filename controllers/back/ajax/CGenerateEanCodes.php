<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\barcode\CBarCodeEan13;

/**
 * Class CGenerateEanCodes
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Iwes  International Web Ecommerce ServicesTeam <juri@iwes.it>
 *
 * @copyright (c) Iwes International Web Ecommerce Services - All rights reserved
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
	    if(strlen($start) < 12) throw new \Exception('Wrong size for start');
	    $end = $this->app->router->request()->getRequestData('end');
	    if(strlen($end) < 12) throw new \Exception('Wrong size for end');
	    $counter = 0;
	    for(;$start<$end;$start++) {
	    	$generator = new CBarCodeEan13();
		    $generator->generate($start);
		    try {
			    $this->app->dbAdapter->insert('EanBucket',['ean'=>(string) $generator]);
			    $counter++;
		    } catch (\Throwable $e) {
		    }
	    }

	    return $counter;
    }
}