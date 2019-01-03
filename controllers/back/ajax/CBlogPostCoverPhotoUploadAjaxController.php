<?php
namespace bamboo\controllers\back\ajax;

//TODO upload photo to amazon and return url

use bamboo\blueseal\business\CDataTables;
use bamboo\core\exceptions\BambooException;
use bamboo\core\intl\CLang;
use bamboo\core\utils\amazonPhotoManager\ImageManager;
use bamboo\core\utils\amazonPhotoManager\S3Manager;
use bamboo\core\utils\slugify\CSlugify;
use bamboo\domain\entities\CPost;
use bamboo\domain\entities\CPostTranslation;

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
class CBlogPostCoverPhotoUploadAjaxController extends AAjaxController
{
	public function post()
	{
	    /** @var CPost $post */
	    $post = \Monkey::app()->repoFactory->create('Post')->findOneBy(['id'=>$this->data['postId'], 'blogId'=>$this->data['blogId']]);

	    /** @var CPostTranslation $postTranslation */
	    $postTranslation = $post->postTranslation->getFirst();

        $fileFolder =  $this->app->rootPath().$this->app->cfg()->fetch('paths', 'blogImages') . '/';
	    if(!is_null($postTranslation->coverImage)){
	        unlink($fileFolder . $postTranslation->coverImage);
        }

		$files = $this->app->router->request()->getFiles();

        $s = new CSlugify();
        $pathinfo = pathinfo($files['file']['name'][0]);
        $uploadfile = rand(0, 9999999999) . '-' .$s->slugify($pathinfo['filename']).'.'. $pathinfo['extension'];
        if (!rename($files['file']['tmp_name'][0], $fileFolder . $uploadfile)) throw new \Exception();
        $postTranslation->coverImage = $uploadfile;
        $postTranslation->update();

        return true;
	}
}