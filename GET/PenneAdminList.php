<?php

namespace Acms\Plugins\Penne\GET;

use ACMS_GET;
use Template;
use DB;
use SQL;
use SQL_Select;
use ACMS_Corrector;

class PenneAdminList extends ACMS_GET
{
  public $limit = 20;

  function get()
  {
    $Tpl = new Template($this->tpl, new ACMS_Corrector());

    // カスタムフィールド一覧を取得
    $DB = DB::singleton(dsn());
    $SQL = SQL::newSelect('penne_group');
    $SQL->addSelect('*');
    $SQL->addOrder('penne_group_sort', 'DESC');

    // ページャを取得
    $Pager = new SQL_Select($SQL);
    $Pager->setSelect('DISTINCT(penne_group_id)', 'group_amount', null, 'COUNT');
    $pageAmount = intval($DB->query($Pager->get(dsn()), 'one'));
    $pager = $this->buildPager(PAGE, $this->limit, $pageAmount, 3, ' class="cur"', $Tpl, array(), array('admin' => ADMIN));

    // クエリを実行
    $SQL->setLimit($this->limit, (PAGE - 1) * $this->limit);
    $group = $DB->query($SQL->get(dsn()), 'all');
    foreach ($group as $key => $value) {
      $group[$key]['itemUrl'] = acmsLink(array(
        'bid' => BID,
        'admin' => 'app_penne_editor',
        'query' => array(
          'id'   => $value['penne_group_id'],
        ),
      ));
      $group[$key]['penne_group_status#'.$value['penne_group_status']] = (object)[];
    }
  
    // テンプレートに格納
    $vars = array(
      'group'       => $group,
      'pager'       => $pager,
      'itemsAmount' => $pager['itemsAmount'],
      'itemsFrom'   => $pager['itemsFrom'],
      'itemsTo'     => $pager['itemsTo'],
    );

    return $Tpl->render($vars);
  }
}
