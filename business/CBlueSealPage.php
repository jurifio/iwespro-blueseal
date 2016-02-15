<?php

namespace bamboo\blueseal\business;

use bamboo\core\application\AApplication;
use bamboo\core\exceptions\RedPandaConfigException;

/**
 * Class CBlueSealPage
 * @package bamboo\blueseal\business
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>, ${DATE}
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */
class CBlueSealPage
{
    /**
     * @var \bamboo\core\db\pandaorm\entities\IEntity
     */
    protected $data;

	/**
	 * CBlueSealPage constructor.
	 * @param $pageSlug
	 * @param AApplication $app
	 * @throws RedPandaConfigException
	 */
    public function __construct($pageSlug, AApplication $app)
    {
        if (empty($pageSlug)) {
            throw new RedPandaConfigException('Page slug not set in controller');
        }
        $this->data = $app->repoFactory->create('Page')->findOneBy(['slug'=>$pageSlug]);
        if(empty($this->data)) throw new RedPandaConfigException('Page config not found for '.$pageSlug);
    }

    /**
     * @return mixed
     */
    public function getPermissionPath()
    {
        return $this->data->permission;
    }

    /**
     * @return mixed
     */
    public function getIcon()
    {
        return $this->data->icon;
    }

    /**
     * @return mixed
     */
    public function getSlug()
    {
        return $this->data->slug;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->data->url;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->data->pageTranslation->getFirst()->title;
    }

    /**
     * @return string
     */
    public function getDescripion()
    {
        return $this->data->pageTranslation->getFirst()->description;
    }

    /**
     * @return string
     */
    public function getKeywords()
    {
        return $this->data->pageTranslation->getFirst()->keywords;
    }
}