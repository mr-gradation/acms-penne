<?php

namespace Acms\Plugins\Penne;

class Hook
{

    /**
     * GETモジュール処理前
     * 解決前テンプレートの中間処理など
     *
     * @param string &$tpl
     * @param \ACMS_GET $thisModule
     */
    public function beforeBuild(&$tpl)
    {
        $tpl = preg_replace(
            '/<div id="acms_box3" class="acms-admin-tabs-panel anchorFix">([\s\S]*?)<\/div>/', 
            '<div id="acms_box3" class="acms-admin-tabs-panel anchorFix">$1<!-- BEGIN_MODULE Admin_InjectTemplate id="admin-module-config-module" --><!-- END_MODULE Admin_InjectTemplate --></div>',
            $tpl
        );
    }

}
