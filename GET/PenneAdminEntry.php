<?php

namespace Acms\Plugins\Penne\GET;

use ACMS_GET_Admin;
use Template;
use DB;
use SQL;
use ACMS_Corrector;

class PenneAdminEntry extends ACMS_GET_Admin
{
  function get()
  {
    $Tpl = new Template($this->tpl, new ACMS_Corrector());
    $DB = DB::singleton(dsn());
    $vars = array();


    // 編集モードを取得
    $edit = ($this->Post->isExists('edit')) ? $this->Post->get('edit') : $this->Get->get('edit');
    if ($edit) {
      $vars['submit#'.$edit] = (object)[];
    }

    // データを取得
    $group_id = ($this->Post->isExists('id')) ? $this->Post->get('id') : $this->Get->get('id');
    $vars['group_id'] = $group_id;

    if ( !$this->Post->isNull() ) {
      // POST送信結果を格納
      $vars += $this->buildField($this->Post, $Tpl);
    } else if ($group_id) {
      $SQL = SQL::newSelect('penne_group');
      $SQL->addSelect('*');
      $SQL->addWhereOpr('penne_group_id', $group_id, '=');
      $group = $DB->query($SQL->get(dsn()), 'row');
      $vars += $group;
      $vars['penne_group_status:checked#'.$group['penne_group_status']] = 'checked';
      $vars['penne_group_location:checked#'.$group['penne_group_location']] = 'checked';
      
      // フィールドを取得
      $SQL = SQL::newSelect('penne_field');
      $SQL->addSelect('*');
      $SQL->addWhereOpr('penne_field_group_id', $group_id, '=');
      $fields = $DB->query($SQL->get(dsn()), 'all');
      foreach ($fields as $key => $value) {
        $fields[$key]['penne_field_element:checked#'.$fields[$key]['penne_field_element']] = 'checked';
      }
      $vars['penne_field'] = $fields;
    }

    // ブログを取得
    $SQL = SQL::newSelect('blog');
    $SQL->addSelect('blog_id');
    $SQL->addSelect('blog_name');
    $SQL->addOrder('blog_sort', 'ASC');
    $blog = $DB->query($SQL->get(dsn()), 'all');
    $vars['blog'] = $blog;
    
    // カテゴリーを取得
    $vars += $this->buildCategorySelect($Tpl, BID, null, 'loop');
    
    // 一覧に戻るリンク
    $vars['indexUrl'] = acmsLink(array(
      'bid' => BID,
      'admin' => 'app_penne_index'
    ));

    // var_dump($vars);

    return $Tpl->render($vars);
  }
}
