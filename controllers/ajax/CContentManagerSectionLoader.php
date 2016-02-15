<?php
namespace bamboo\controllers\ajax;

use bamboo\ecommerce\views\VBase;
use bamboo\core\intl\CLang;
use bamboo\core\io\CJsonAdapter;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;

/**
 * Class CContentManagerSectionLoader
 * @package bamboo\app\controllers
 */
class CContentManagerSectionLoader extends AAjaxController
{
    public function get()
    {
        throw new \Exception();
    }

    public function post()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/widgets/section.php');
        $root = $this->app->cfg()->fetch('paths','root');
        $this->app->setLang(new CLang(1,'it'));

        $repo = $this->app->repoFactory->create('Lang');
        $installedLang = $repo->findAll();

        $json = new CJsonAdapter($root.'/htdocs/pickyshop/blueseal/content/structure.json');
        $languages['it'] = true;
        $return = "";

        foreach ($json['sections'][$this->data['section']] as $jsonFile => $descriptor) {
            foreach ($installedLang as $lang) {
                if (!file_exists($root.'/htdocs/pickyshop/app/data/widget/'.$jsonFile.'.'.$lang->lang.'.json')) {
                    copy($root.'/htdocs/pickyshop/app/data/widget/'.$jsonFile.'.it.json',$root.'/htdocs/pickyshop/app/data/widget/'.$jsonFile.'.'.$lang->lang.'.json');
                    $languages[$lang->lang] = false;
                }
            }

            $translationStatus = isset($descriptor['translations']) ? $descriptor['translations'] : $languages;
            $text = $descriptor['cmsDescription'];
            $icon = $descriptor['cmsIcon'];

            $items = new CJsonAdapter($root.'/htdocs/pickyshop/app/data/widget/'.$jsonFile.'.it.json');
            unset($items['global']);

            $date = new \DateTime();
            $date->setTimestamp($items->lastUpdated());
            $lastUpdate = $date->format('d-m-Y H:i');

            foreach ($items as $key => $value) {

                $content = "";
                foreach ($descriptor['cmsDescriptionField'] as $descriptionField) {
                    $content .= $value[$descriptionField]." ";
                }

                $return .= $view->render([
                    'app' => new CRestrictedAccessWidgetHelper($this->app),
                    'widgetType' => $jsonFile,
                    'widgetId' => $key,
                    'schema' => $descriptor,
                    'data' => $value,
                    'description' => $descriptor['cmsDescription'],
                    'content' => $content,
                    'lastUpdate' => $lastUpdate,
                    'translationStatus' => $translationStatus,
                    'icon' => $icon,
                    'section' => $this->data['section']
                ]);
            }
        }

        return $return;
    }

    public function delete()
    {
        throw new \Exception();
    }
}