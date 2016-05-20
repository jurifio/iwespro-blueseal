<?php
namespace bamboo\blueseal\controllers\ajax;

//TODO upload photo to amazon and return url

use bamboo\blueseal\business\CDataTables;
use bamboo\core\exceptions\BambooException;
use bamboo\core\intl\CLang;
use bamboo\core\utils\amazonPhotoManager\ImageManager;
use bamboo\core\utils\amazonPhotoManager\S3Manager;
use bamboo\core\utils\slugify\CSlugify;

/**
 * Class CBlogPostTrashListAjaxController
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 29/04/2016
 * @since 1.0
 */
class CBlogPostPhotoUploadAjaxController extends AAjaxController
{
	public function post()
	{
		$this->app->vendorLibraries->load("amazon2723");
		$config = $this->app->cfg()->fetch('miscellaneous', 'amazonConfiguration');

		$tempFolder = $this->app->rootPath().$this->app->cfg()->fetch('paths', 'tempFolder')."/";

		$files = $this->app->router->request()->getFiles();
		$image = new S3Manager($config['credential']);

		$s = new CSlugify();
		$fileName = $files['file']['name']+crc32(rand(0,10010010101)).'.'.pathinfo($files['file']['name'], PATHINFO_EXTENSION);
		$fileName = $s->slugify($fileName);
		if (!rename($files['file']['tmp_name'], $tempFolder . $fileName)) {
			throw new BambooException('Cannot move the uploaded Files');
		}

		try{
			$res = $image->putImage($config['bucket'], $tempFolder . $fileName, $this->app->getName()."-blog", $fileName);
		}catch(\Exception $e){
			$this->app->router->response()->raiseProcessingError();
			return 'Errore nell\'upload del file';
		}
		return $res->get('ObjectURL');
	}
}