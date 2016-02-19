<?php

namespace bamboo\blueseal\business;

use bamboo\blueseal\business\forms\CGenericHTMLTag,
    bamboo\blueseal\business\forms\CInput,
    bamboo\blueseal\business\forms\CSelect,
    bamboo\blueseal\business\forms\CTextarea,
    bamboo\blueseal\business\forms\CImg,
    bamboo\blueseal\business\forms\CWidgetStructureFormField;

/**
 * Class CWidgetStructure
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
class CWidgetStructure
{
    /**
     * @var array
     */
    public $cms;
    /**
     * @var array
     */
    public $text;
    /**
     * @var array
     */
    public $buttons;
    /**
     * @var array
     */
    public $files;
    /**
     * @var array
     */
    public $genericOptions;
    /**
     * @var array
     */
    public $ignore;
    /**
     * @var string
     */
    public $id;
    /**
     * @var string
     */
    public $widgetConfig;

    /**
     * CWidgetStructure constructor.
     * @param array $widgetStructure
     * @param string $id
     */
    public function __construct(array $widgetStructure, $id)
    {
        $this->id = $id;
        foreach ($widgetStructure as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * @return string
     * @deprecated
     */
    public function id()
    {
        return $this->id;
    }

    /**
     * @param $key
     * @return array
     */
    public function get($key)
    {
        return $this->$key;
    }

    /**
     * @param $wc
     */
    public function setWidgetConfig($wc)
    {
        $this->widgetConfig = $wc;
    }

    /**
     * @param string $key
     * @param string $wlang
     * @param string $wpath
     * @param string $assetPath
     * @return string
     */
    public function makeForm($key, $wlang, $wpath, $assetPath = '')
    {
        $data = null;
        \BlueSeal::dump($wpath);
        throw new \Exception();
        if (file_exists($wpath.$this->get('id').'.'.$wlang.'.json')) {

            $data = json_decode(file_get_contents($wpath.$this->get('id').'.'.$wlang.'.json'));
            $data = $data->{$this->widgetConfig};
        }

        $descriptors = $this->get($key);
        $html = "";
        foreach ($descriptors as $id => $descriptor) {

            $isRequired = ($descriptor['required'] === true) ? 'required' : null;
            $wrapperCss = ['form-group', 'form-group-default'];
            if (!is_null($isRequired)) {
                $wrapperCss[] = 'required';
            }

            $wrapper = new CGenericHTMLTag('div', $wrapperCss);
            $d = new CWidgetStructureFormField($id, $descriptor);
            $input = $d->create();

            $input->wsf()->addAttr('id',$input->wsf()->id());
            $input->wsf()->addAttr('name',$input->wsf()->id());

            if ($data !== null) {
                if ($key == 'grids') {
                    $bscols = explode(' ',$data->grid);
                    foreach ($bscols as $bscol) {
                        $bscoldata = explode('-',$bscol);
                        if ($bscoldata[1] == $input->wsf()->id()) {
                            $input->wsf()->setValue($bscoldata[2]);
                        }
                    }
                } else if ($key == 'animations') {
                    $input->wsf()->setValue(explode(' ', $data->{$input->wsf()->id()})[2]);
                } else {
                    $input->wsf()->setValue($data->{$input->wsf()->id()});
                }
            }

            if ($input instanceof CTextarea) {
                $input->wsf()->addCss('height', '100px');
            }

            if ($input instanceof CSelect) {
                $wrapper->addClass('selectize-enabled');
                $input->wsf()->addAttr('placeholder', 'seleziona');
                $input->wsf()->addAttr('tabindex', '-1');
                $input->wsf()->addData('init-plugin', 'selectize');
            }

            if ($input instanceof CInput && $input->wsf()->getWsdField('type') == 'radio') {
                $wrapper->addClass('radio');
                $wrapper->addClass('radio-success');

                $radios = [];
                foreach ($descriptor['values'] as $radio) {
                    $radios[] = [
                        new CGenericHTMLTag('input', [], ['type="radio"', 'name="' . $id . '"', 'id="' . $radio . '"']),
                        new CGenericHTMLTag('label', [], ['for="' . $radio . '"'])
                    ];
                }

                $i = 0;
                $html .= $wrapper->open();
                foreach ($radios as $radio) {
                    $radio[0]->setValue($descriptor['values'][$i]);
                    if (($i + 1) == count($radios)) {
                        $radio[0]->addAttr('checked', 'checked');
                    }
                    $radio[1]->setValue($descriptor['prettyValues'][$i]);
                    $html .= $radio[0]->open() . $radio[0]->close();
                    $html .= $radio[1]->open() . $radio[1]->close();
                    $i++;
                }
                $html .= $wrapper->close();
            } else if ($input instanceof CInput && $input->wsf()->getWsdField('type') == 'file') {
                $wrapper->addClass('form-group-photo');
                $img = new CImg('img', ['img-responsive'], ['align="center"']);
                if ($input->wsf()->getValue()) {
                    $img->setValue($assetPath.'/'.$wlang.'/assets/'.$input->wsf()->getValue());
                }

                $html .= $wrapper->open();
                $html .= $img->open() . $img->close();
                $html .= '<div style="display:none;">' . $input->open() . $input->close() . '</div>';
                $html .= $wrapper->close();
            } else {
                $html .= $wrapper->open();
                $html .= $input->open() . $input->close();
                $html .= $wrapper->close();
            }
        }

        return $html;
    }
}