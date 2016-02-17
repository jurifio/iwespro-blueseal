<?php

namespace bamboo\blueseal\controllers;

use bamboo\core\asset\CAssetBundle;
use bamboo\core\asset\CAssetFinder;
use bamboo\core\exceptions\RedPandaAssetException;
use bamboo\core\router\ARootController;
use bamboo\core\application\AApplication;
/**
 * Class CAssetController
 * @package bamboo\assets\controllers
 */
class CBluesealAssetController extends ARootController
{
	protected $assetFinder;

    public function __construct(AApplication $app, $action)
    {
        $this->app = $app;
        $this->request = $app->router->request();
        $this->response = $app->router->response();
        $this->action = $action;
	    $this->assetFinder = new CAssetFinder($app);
    }

    /**
     * @param $action
     * @return null
     */
	public function createAction($action)
	{
		$this->{$action}($this->app->router->getMatchedRoute()->getComputedFilters());
		return;
	}

    /**
     * @param array $args
     * @throws RedPandaAssetException
     */
	public function get($args)
	{
        $asset = $this->assetFinder->getAsset(basename($args['file']));

        if ($asset == false) {
            $this->response->raiseRoutingError()->sendHeaders();
            return;
        }

        switch($asset->getType()) {
            case "css":
                $this->response->setContentType('text/css');
                break;
            case "js":
                $this->response->setContentType('application/javascript');
                break;
            case "jpg":
            case "jpeg":
                $this->response->setContentType('image/jpg');
                $this->response->setCache("no-transform,public,max-age=3600,s-maxage=900");
                $this->response->setLastModified(filemtime($asset->getPath()));
                $this->response->sendHeaders();
                readfile($asset->getPath());
                return;
            case "gif":
                $this->response->setContentType('image/gif');
                $this->response->setCache("no-transform,public,max-age=3600,s-maxage=900");
                $this->response->setLastModified(filemtime($asset->getPath()));
                $this->response->sendHeaders();
                readfile($asset->getPath());
                return;
            case "png":
                $this->response->setContentType('image/png');
                $this->response->setCache("no-transform,public,max-age=3600,s-maxage=900");
                $this->response->setLastModified(filemtime($asset->getPath()));
                $this->response->sendHeaders();
                readfile($asset->getPath());
                return;
            case "eot":
                $this->response->setContentType('application/vnd.ms-fontobject');
                $this->response->setCache("no-transform,public,max-age=3600,s-maxage=900");
                $this->response->setLastModified(filemtime($asset->getPath()));
                $this->response->sendHeaders();
                readfile($asset->getPath());
                return;
            case "ttf":
            case "otf":
                $this->response->setContentType('application/font-sfnt');
                $this->response->setCache("no-transform,public,max-age=3600,s-maxage=900");
                $this->response->setLastModified(filemtime($asset->getPath()));
                $this->response->sendHeaders();
                readfile($asset->getPath());
                return;
            case "svg":
                $this->response->setContentType('image/svg+xml');
                $this->response->setCache("no-transform,public,max-age=3600,s-maxage=900");
                $this->response->setLastModified(filemtime($asset->getPath()));
                $this->response->sendHeaders();
                readfile($asset->getPath());
                return;
            case "woff":
                $this->response->setContentType('application/font-woff');
                $this->response->setCache("no-transform,public,max-age=3600,s-maxage=900");
                $this->response->setLastModified(filemtime($asset->getPath()));
                $this->response->sendHeaders();
                readfile($asset->getPath());
                return;
            case "woff2":
                $this->response->setContentType('application/font-woff2');
                $this->response->setCache("no-transform,public,max-age=3600,s-maxage=900");
                $this->response->setLastModified(filemtime($asset->getPath()));
                $this->response->sendHeaders();
                readfile($asset->getPath());
                return;
	        default:
		        $this->response->setContentType($asset->getMime());
		        $this->response->setLastModified(filemtime($asset->getPath()));
		        $this->response->sendHeaders();
		        readfile($asset->getPath());
		        return;
        }

        /**
         * Looking for packed asset file with filename pattern <crc32>.filename.ext...
         */
		$a = $this->app->rootPath().$this->app->cfg()->fetch('paths','packed-assets').'*.'.$args['file'];
        foreach(glob($this->app->rootPath().$this->app->cfg()->fetch('paths','packed-assets').'*.'.$args['file']) as $file) {
            $this->response->setCache("no-transform,public,max-age=300,s-maxage=900");
            $this->response->setLastModified(filemtime($file));
            $this->response->sendHeaders();
            readfile($file);
            return;
        }

        /**
         * ...if not found, looking for packed asset file...
         */
        foreach(glob($this->app->rootPath().$this->app->cfg()->fetch('paths','packed-assets').$args['file']) as $file) {
            $this->response->setCache("no-transform,public,max-age=300,s-maxage=900");
            $this->response->setLastModified(filemtime($file));
            $this->response->sendHeaders();
            readfile($file);
            return;
        }

        /**
         * ...if not found, looking for css files in theme folder...
         */
        foreach(glob($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/assets/css/'.$args['file']) as $file) {
            $this->response->setCache("no-transform,public,max-age=300,s-maxage=900");
            $this->response->setLastModified(filemtime($file));
            $this->response->sendHeaders();
            readfile($file);
            return;
        }

        /**
         * ...if not found, looking for js files in theme folder
         */
        foreach(glob($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/assets/js/'.$args['file']) as $file) {
            $this->response->setCache("no-transform,public,max-age=300,s-maxage=900");
            $this->response->setLastModified(filemtime($file));
            $this->response->sendHeaders();
            readfile($file);
            return;
        }
        //var_dump($this->app);
	}

    public function __destruct() {}
}