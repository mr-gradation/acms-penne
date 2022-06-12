<?php

namespace Acms\Plugins\Penne\GET;

use ACMS_GET;
use Template;
use ACMS_Corrector;

class PenneAdminEntryField extends ACMS_GET
{
  function get()
  {
    // 記事詳細画面ではInjectTemplateでモジュールが使用できないのを調整
    $template = file_get_contents(__DIR__.'/../theme/field_entry.html');
    $component = file_get_contents(__DIR__.'/../theme/component.html');
    $component = str_replace('\\', '', $component);
    $template = str_replace('@include("component.html")', $component, $template);
    
    $Tpl = new Template($template, new ACMS_Corrector());
    $engine = \App::make('penne.engine');
    $vars['group'] = $engine->getField('entry');
  
    return $Tpl->render($vars);
  }
}
