<?php
namespace bamboo\controllers\back\ajax;

/**
 * Class CNameTranslateManager
 * @package redpanda\blueseal\controllers\ajax
 *
 * @author Iwes  International Web Ecommerce ServicesTeam <juri@iwes.it>
 *
 * @copyright (c) Iwes International Web Ecommerce Services - All rights reserved
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
        $repo = \Monkey::app()->repoFactory->create('Lang');
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