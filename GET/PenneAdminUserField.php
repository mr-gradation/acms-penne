<?php

namespace Acms\Plugins\Penne\GET;

use ACMS_GET;
use Template;
use ACMS_Corrector;

class PenneAdminUserField extends ACMS_GET
{
  function get()
  {

    $Tpl = new Template($this->tpl, new ACMS_Corrector());

    $engine = \App::make('penne.engine');
    $vars['group'] = $engine->getField('user');
  
    return $Tpl->render($vars);
  }
}
