<?php
namespace bamboo\controllers\ajax;

use bamboo\ecommerce\views\VBase;
use bamboo\core\intl\CLang;
use bamboo\core\io\CJsonAdapter;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;

/**
 * Class CContentManagerContentForm
 * @package bamboo\app\controllers
 */
class CContentManagerContentForm extends AAjaxController
{
    /**
     * @var array
     */
    protected $jsonData = [];

    /**
     * @var array
     */
    protected $processedFields = [];

    /**
     * @throws \Exception
     */
    public function get()
    {
        throw new \Exception();
    }

    /**
     * @return string
     */
    public function post()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/widgets/contentEditor.php');
        $root = $this->app->cfg()->fetch('paths','root');
        $approot = $this->app->cfg()->fetch('paths','app');
        $this->app->setLang(new CLang(1,'it'));

        $jsonDescriptor = new CJsonAdapter($root.'/htdocs/pickyshop/blueseal/content/structure.json');
        $repo = $this->app->repoFactory->create('Lang');
        $installedLang = $repo->findAll();

        foreach ($installedLang as $lang) {
            $this->jsonData[$lang->lang] = new CJsonAdapter($approot.'/data/widget/'.$this->data['type'].'.'.$lang->lang.'.json');
            foreach ($jsonDescriptor['sections'][$this->data['section']][$this->data['type']] as $type => $content) {
                if (strpos($type,'cms') !== 0 && $type !== 'ignore') {
                    $this->createFields($type, $content, $lang->lang);
                }
            }
        }

        $return = "";
        $return .= $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'langs' => $installedLang,
            'fields' => $this->processedFields
        ]);

        return $return;
    }

    /**
     * @param $type
     * @param $descriptors
     * @param $lang
     */
    private function createFields($type, $descriptors, $lang)
    {
        $this->{'createFieldsFor'.ucfirst($type)}($descriptors, $lang);
    }

    /**
     * @param $descriptors
     * @param $lang
     */
    private function createFieldsForText($descriptors,$lang)
    {
        $output = "";

        foreach ($descriptors as $id => $content) {

            $value = $this->jsonData[$lang][$this->data['id']][$id];

            switch ($content['type']) {
                case "text":
                    $openTag = "<input ";
                    $closeTag = "/>";
                    $rows = "";
                    break;
                case "textarea":
                    $openTag = "<textarea ";
                    $closeTag = ">".$value."</textarea>";
                    $rows = "style=\"height:4em\"";
                    break;
                default:
                    $openTag = "<input ";
                    $closeTag = "/>";
                    $rows = "";
            }

            $output .= '<div class="form-group form-group-default">';
            $output .= '<label for="'.$id.'">'.$content['label'].'</label>';
            $output .= $openTag."type=\"".$content['type']."\" id=\"".$id."\" name=\"".$id."\" value=\"".$value."\" ".$rows." class=\"form-control\" autocomplete=\"off\" ".$closeTag;
            $output .= '</div>';
        }

        $this->attachFields($output,'text',$lang);
    }

    /**
     * @param $descriptors
     * @param $lang
     */
    private function createFieldsForButtons($descriptors,$lang)
    {
        $output = "";
        foreach ($descriptors as $id => $content) {

            $value = $this->jsonData[$lang][$this->data['id']][$id];
            $hrefValue = $this->jsonData[$lang][$this->data['id']][$content['href']];

            $type = $content['type'];
            $label = $content['label'];
            $href = $content['href'];

            $output .= "<div class=\"form-group form-group-default\">";
            $output .= "<label for=\"$id\">$label</label>";
            $output .= "<input type=\"$type\" id=\"$id\" name=\"$id\" value=\"$value\" class=\"form-control\" autocomplete=\"off\" />";
            $output .= "</div>";

            $output .= "<div class=\"form-group form-group-default\">";
            $output .= "<label for=\"$href\">Link $label</label>";
            $output .= "<input type=\"$type\" id=\"$href\" name=\"$href\" value=\"$hrefValue\" class=\"form-control\" autocomplete=\"off\" />";
            $output .= "</div>";
        }
        $this->attachFields($output,'buttons',$lang);
    }

    /**
     * @param $descriptors
     * @param $lang
     */
    private function createFieldsForFiles($descriptors,$lang)
    {
        $output = "";
        $sidebar = "";
        foreach ($descriptors as $id => $content) {

            $label = $content['label'];
            $linkId = $content['href'];
            $altId = $content['alt'];

            if ($content['type'] == 'image') {

                $image = $this->jsonData[$lang][$this->data['id']][$id];
                $alt = $this->jsonData[$lang][$this->data['id']][$content['alt']];
                $href = $this->jsonData[$lang][$this->data['id']][$content['href']];

                $output .= "<div class=\"form-group form-group-default\">";
                $output .= "<label for=\"$id\">File</label>";
                $output .= "<input type=\"file\" id=\"$id\" name=\"$id\" value=\"\" class=\"form-control\" autocomplete=\"off\" />";
                $output .= "</div>";

                $output .= "<div class=\"form-group form-group-default\">";
                $output .= "<label for=\"$altId\">Testo alternativo $label (accessibilità)</label>";
                $output .= "<input type=\"text\" id=\"$altId\" name=\"$altId\" value=\"$alt\" class=\"form-control\" autocomplete=\"off\" />";
                $output .= "</div>";

                $output .= "<div class=\"form-group form-group-default\">";
                $output .= "<label for=\"$linkId\">Link $label</label>";
                $output .= "<input type=\"text\" id=\"$linkId\" name=\"$linkId\" value=\"$href\" class=\"form-control\" autocomplete=\"off\" />";
                $output .= "</div>";

                $sidebar .= "<div class=\"image-preview\">";
                $sidebar .= "<img src=\"http://www.pickyshop.com/it/assets/$image\" width=\"100%\"/>";
                $sidebar .= "</div>";
            }
        }
        $this->attachFields($sidebar,'files',$lang,true);
        $this->attachFields($output,'files',$lang,false);
    }

    /**
     * @param $descriptors
     * @param $lang
     */
    private function createFieldsForGenericOptions($descriptors,$lang)
    {
        $output = "";
        foreach ($descriptors as $id => $content) {

            $optionSelected = $this->jsonData[$lang][$this->data['id']][$content['name']];
            $name = $content['name'];
            $fieldLabel = $content['label'];

            $output .= "<div class=\"form-group form-group-default\">";
            $output .= "<label>$fieldLabel</label>";
            $output .= "<div class=\"radio radio-success\">";

            $i = 0;
            foreach ($content['values'] as $optionValue) {
                $checked = null;
                if ($optionSelected == $optionValue) {
                    $checked = "checked=\"checked\"";
                }
                $label = $content['prettyValues'][$i];
                $output .= "<input type=\"radio\" value=\"$optionValue\" name=\"$name\" id=\"$label\" $checked />";
                $output .= "<label for=\"$label\">$label</label>";
                $i++;
            }
            $output .= "</div>";
            $output .= "</div>";
        }

        $this->attachFields($output,'generic',$lang);
    }

    /**
     * @param $content
     * @param $type
     * @param $lang
     * @param bool|false $sidebar
     */
    public function attachFields($content, $type, $lang, $sidebar = false)
    {
        if ($sidebar === false) {
            $this->processedFields['main'][$type][$lang] = $content;
        } else {
            $this->processedFields['sidebar'][$type][$lang] = $content;
        }
    }

    /**
     * @throws \Exception
     */
    public function delete()
    {
        throw new \Exception();
    }
}