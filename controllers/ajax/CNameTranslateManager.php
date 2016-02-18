<?php
namespace bamboo\blueseal\controllers\ajax;

/**
 * Class CNameTranslateManager
 * @package redpanda\blueseal\controllers\ajax
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date $date
 * @since 1.0
 */
class CNameTranslateManager extends AAjaxController
{
    /**
     * @return string
     */
    public function get()
    {
        $repo = $this->app->repoFactory->create('Lang');
        $installedLang = $repo->findAll();
        $html = '<div class="col-sm-6">';
        $html .= '<div class="form-group form-group-default radio radio-success">';

        foreach ($installedLang as $lang) {
            $html .= '<input type="radio" id="lang_' . $lang->id . '" name="langId" value="' . $lang->id . '" ';
            if ($lang->id == '1') {
                $html .= 'checked="checked"';
            }
            $html .= ' />';
            $html .= '<label for="lang_' . $lang->id . '">' . $lang->name . '</label><br>';
        }
        $html .= '</div></div>';

        return json_encode(
            [
                'status' => 'ok',
                'bodyMessage' => $html,
                'okButtonLabel' => 'Esegui',
                'cancelButtonLabel' => 'Annulla'
            ]
        );
    }

}