<?php
namespace bamboo\controllers\back\ajax;

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

		$tempFolder = $this->app->rootPath().$this->app->cfg()->fetch('paths', 'tempFolder').'-blog/';
        $origintempFolder = $this->app->rootPath().$this->app->cfg()->fetch('paths', 'tempFolder').'-blog';

		$files = $this->app->router->request()->getFiles();
        $name = md5(rand(100, 200));
        $ext = explode('.', $files['file']['name']);
        $filename = $name . '.' . $ext[1];
        $destination = $tempFolder . $filename; //change this directory
        $location = $files["file"]["tmp_name"];
        move_uploaded_file($location, $destination);
        $newname=crc32(rand(0,10010010101)).'-'.$files["file"]["name"];
        rename($destination,$tempFolder.$newname);
        $newdestination=$tempFolder.$newname;
		$image = new S3Manager($config['credential']);

		/*$s = new CSlugify();
		$fileName = pathinfo($files['file']['name'], PATHINFO_FILENAME) .'-'. crc32(rand(0,10010010101));
		$fileName = $s->slugify($fileName).'.'.pathinfo($files['file']['name'], PATHINFO_EXTENSION);
		if (!rename($origintempFolder .$files['file']['tmp_name'], $tempFolder . $fileName)) {
			throw new BambooException('Cannot move the uploaded Files');
		}*/

		try{
          //  $res = $image->putImage($config['bucket'], $dummyFileFolder . '/' . $original, 'dummy', $original);
			$res = $image->putImage($config['bucket'], $newdestination, 'iwes-blog', $newname);
            //$res = $image->processImageEditorialUploadPhoto($_FILES['file']['name'][$i], $fileName, $config['bucket'].'-editorial', 'plandetail-images');
		}catch(\Throwable $e){
			$this->app->router->response()->raiseProcessingError();
			return 'Errore nell\'upload del file';
		}
		return $res->get('ObjectURL');
	}
}