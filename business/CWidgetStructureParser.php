<?php

namespace bamboo\blueseal\business;

use bamboo\core\application\AApplication;
use bamboo\core\base\CObjectCollection;
use bamboo\core\io\CJsonAdapter;

/**
 * Class CWidgetStructureParser
 * @package bamboo\blueseal\business
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 19/11/2015
 * @since 1.0
 */
class CWidgetStructureParser
{
    /**
     * @var CJsonAdapter
     */
    protected $data;

    /**
     * @var CObjectCollection
     */
    protected $widgets;

    /**
     * @var CObjectCollection
     */
    protected $languages;

    /**
     * @var string
     */
    protected $bluesealPath;

    /**
     * @var string
     */
    protected $appPath;

    /**
     * @var array
     */
    protected $dtDrawId;

    /**
     * CWidgetStructureParser constructor.
     * @param AApplication $app
     * @param CObjectCollection $languages
     * @param $sectionName
     */
    public function __construct(AApplication $app, CObjectCollection $languages, $sectionName)
    {
        $this->bluesealPath = $app->rootPath().$app->cfg()->fetch('paths','blueseal');
        $this->themePath = $app->rootPath().$app->cfg()->fetch('paths','store-theme');
        $this->data = new CJsonAdapter($this->bluesealPath.'/content/structure.json');
        $this->widgets = new CObjectCollection();
        $this->languages = $languages;
        $this->dtDrawId = $app->router->request()->getRequestData('draw');
        $this->appPath = $app->rootPath().$app->cfg()->fetch('paths','app');
        $this->app = $app->rootPath();
        \BlueSeal::dump($app->rootPath());
        throw new \Exception();
        foreach ($this->data['sections'][$sectionName] as $widget => $data) {
            $this->widgets->add(new CWidgetStructure($data,$widget));
        }
    }

    /**
     * @param $widgetName
     * @param $widgetKey
     * @param string $language
     * @return bool
     */
    public function isWidgetActive($widgetName, $widgetKey, $language = 'it')
    {
        foreach ($this->widgets() as $widget) {
            if ($widget->id() === $widgetName) {
                $json = new CJsonAdapter($this->themePath.'/layout/routes.json');
                foreach ($json[$widget->get('route')]['children'] as $activeWidget) {
                    if ($widgetKey === explode('.',$activeWidget)[2]) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    /**
     * @param $widgetName
     * @return CWidgetStructure
     */
    public function getWidget($widgetName)
    {
        return $this->widgets->findOneByKey('id',$widgetName);
    }

    /**
     * @return string
     */
    public function getDTJson()
    {
        $languages['it'] = true;
        $response['draw'] = $this->dtDrawId;
        $response['data'] = [];

        $i = 0;

        foreach ($this->widgets() as $widget) {

            $structureJson = new CJsonAdapter();
            $structureJson->createFromArray($widget->get('cms'));
            $dtArray = [];

            foreach ($this->languages as $lang) {
                \BlueSeal::dump($this->app);
                throw new \Exception();
                if (!file_exists($this->app->rootPath().$this->appPath.'/data/widget/'.$widget->id().'.'.$lang->lang.'.json')) {

                    $languages[$lang->lang] = false;
                } else {
                    $languages[$lang->lang] = true;
                }
            }

            $dtArrayLang = [];
            foreach ($languages as $language => $installed) {
                $dtArrayLang[] = '<span '.(($installed === true) ? 'class="badge"' : 'class="badge badge-red"').'>'.$language.'</span>';
            }

            $widgetJson = new CJsonAdapter($this->appPath.'/data/widget/'.$widget->id().'.it.json');

            foreach ($widgetJson as $k => $v) {
                if ($k !== 'global') {
                    $dtArray[$k]['description'] = $widget->get('cms')['description'];
                    $dtArray[$k]['content'] = '';
                    foreach ($widget->get('cms')['content'] as $cnt) {
                        $dtArray[$k]['content'] .= ' '.$v[$cnt];
                    }
                    $dtArray[$k]['media'] = $v[$widget->get('cms')['media']];
                    $dtArray[$k]['content'] = trim($dtArray[$k]['content']);
                    $dtArray[$k]['icon'] = $widget->get('cms')['icon'];
                    $dtArray[$k]['lang'] = implode(' ',$dtArrayLang);
                }
            }

            $dtJson = new CJsonAdapter();
            $dtJson->createFromArray($dtArray);

            $dataTable = new CJsonDataTables($_GET, $dtJson);

            $filteredJson = $dtJson->prepare($dataTable->getQuery(),$dataTable->getParams());

            $response['recordsTotal'] = $dtJson->count();
            $response['recordsFiltered'] = $dtJson->getSqlFilteredCount();

            foreach ($filteredJson as $item) {

                $response['data'][$i]["DT_RowId"] = 'row__'.$item['id'].'__'.$widget->id();
                $response['data'][$i]["DT_RowClass"] = 'colore';
                $response['data'][$i]['media'] = '<img src="/it/assets/'.$item['media'].'" width="100" />';
                $response['data'][$i]['id'] = '<a data-toggle="tooltip" title="modifica" data-placement="right" href="/blueseal/contenuti/homepage/modifica/it/'.$widget->id().'/'.$item['id'].'">'.$item['id'].'</a>';
                $response['data'][$i]['description'] = '<i class="fa '.$item['icon'].'"></i> '.$item['description'];
                $response['data'][$i]['content'] = $item['content'];
                $response['data'][$i]['lang'] = $item['lang'];
                $response['data'][$i]['active'] = ($this->isWidgetActive($widget->id(),$item['id'])) ? '<span class="text-green">online</span>' : '<span class="text-red">offline</span>';

                $i++;
            }
        }

        return json_encode($response);
    }

    /**
     * @return CObjectCollection
     */
    public function widgets()
    {
        return $this->widgets;
    }

    /**
     * @return CWidgetStructure
     */
    public function toArray()
    {
        return $this->widgets->toArray();
    }

    /**
     * @return int
     */
    public function countWidgets()
    {
        return $this->widgets->count();
    }

    /**
     * @param $widget
     * @return bool
     */
    public function widgetExists($widget)
    {
        return false;
    }


}