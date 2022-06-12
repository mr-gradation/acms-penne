<?php

namespace Acms\Plugins\Penne;

use DB;
use SQL;
use Field_Validation;
use Field;

class Engine
{
  function getField($location)
  {
    // カスタムフィールド一覧を取得
    $DB = DB::singleton(dsn());
    $SQL = SQL::newSelect('penne_group');
    $SQL->addSelect('*');
    $SQL->addWhereOpr('penne_group_location', $location);
    $SQL->addOrder('penne_group_sort', 'DESC');
    $group = $DB->query($SQL->get(dsn()), 'all');

    foreach ($group as $key => $value) {
      // グループの条件を解決
      $group[$key]['penne_group_condition'] = build(setGlobalVars($value['penne_group_condition']), Field_Validation::singleton('post'));
      if ($group[$key]['penne_group_condition'] == '') {
        $group[$key]['penne_group_condition'] = '1/eq/1';
      }
      
      // フィールドの値を取得
      $SQL = SQL::newSelect('penne_field');
      $SQL->addSelect('*');
      $SQL->addWhereOpr('penne_field_group_id', $value['penne_group_id'], '=');
      $fields = $DB->query($SQL->get(dsn()), 'all');
      
      foreach ($fields as $key2 => $value2) {

        // 格納済みの値を取得
        $SQL = SQL::newSelect('field');
        $SQL->addSelect('field_value');
        switch ($location) {
          case 'blog':
            $SQL->addWhereOpr('field_bid', BID);
            break;
          case 'category':
            $SQL->addWhereOpr('field_cid', CID);
            break;
          case 'entry':
            $SQL->addWhereOpr('field_eid', EID);
            break;
          case 'module':
            $Get = new Field(Field::singleton('get'));
            $SQL->addWhereOpr('field_mid', $Get->get('mid'));
            break;
          case 'user':
            $SQL->addWhereOpr('field_uid', UID);
            break;
          default:
            break;
        }
        if ($value2['penne_field_element'] == 'image' || $value2['penne_field_element'] == 'file') {
          $SQL->addSelect('field_key');
          $SQL->addWhereOpr('field_key', $value2['penne_field_varname'] . '@%', 'LIKE');
        } else {
          $SQL->addWhereOpr('field_key', $value2['penne_field_varname']);
        }
        $all = $DB->query($SQL->get(dsn()), 'all');
        
        // 値を配列に変更
        if ($fields[$key2]['penne_field_value']) {
          $array = [];
          foreach (preg_split("/[\s,]+/", $value2['penne_field_value']) as $key3 => $value3) {
            $array[] = array(
              'penne_field_value' => $value3,
              'penne_field_varname' => $fields[$key2]['penne_field_varname']
            );
          }
          $fields[$key2]['penne_field_value'] = $array;
        }

        // 値を格納
        if ($all) {
          // 値を格納
          if (count($all) == 1) {
            $data = array_values($all[0])[0];
          } else {
            $data = [];
            foreach ($all as $key3 => $value3) {
              $data[] = array_values($value3)[0];
            }
          }
          $fields[$key2]['penne_field_data'] = $data;

          // 配列の場合、値を格納
          if (is_array($fields[$key2]['penne_field_value'])) {
            foreach ($fields[$key2]['penne_field_value'] as $key3 => $value3) {
              if (is_array($data)) {
                if (@$data[$key3] == $value3['penne_field_value']) {
                  $fields[$key2]['penne_field_value'][$key3]['checked'] = 'checked';
                  $fields[$key2]['penne_field_value'][$key3]['selected'] = 'selected';
                }
              } else {
                if ($value3['penne_field_value'] == $data) {
                  $fields[$key2]['penne_field_value'][$key3]['checked'] = 'checked';
                  $fields[$key2]['penne_field_value'][$key3]['selected'] = 'selected';
                }
              }
            }
          }

          // リッチエディタ
          if ($value2['penne_field_element'] == 'rich-editor') {
            $json = json_decode($data);
            $fields[$key2]['penne_field_data@html'] = $json->html;
          }

          // メディア
          if ($value2['penne_field_element'] == 'media') {
            $SQL = SQL::newSelect('media');
            $SQL->addSelect('media_type');
            $SQL->addSelect('media_path');
            $SQL->addSelect('media_thumbnail');
            $SQL->addWhereOpr('media_id', $data);
            $media = $DB->query($SQL->get(dsn()), 'row');
            if ($media) {
              $fields[$key2]['penne_field_data@type'] = $media['media_type'];
              $fields[$key2]['penne_field_data@path'] = '/' . MEDIA_LIBRARY_DIR . $media['media_path'];
              $fields[$key2]['penne_field_data@thumbnail'] = $media['media_thumbnail'];
            }
          }
          
          // 画像・ファイル
          if ($value2['penne_field_element'] == 'image' || $value2['penne_field_element'] == 'file') {
            $data = [];
            foreach ($all as $key3 => $value3) {
              $new = str_replace($fields[$key2]['penne_field_varname'], 'penne_field_data', $value3['field_key']);
              $data[$new] = $value3['field_value'];
            }
            $fields[$key2] = array_merge($fields[$key2], $data);
          }
        }

      }
      $group[$key]['field'] = $fields;
    }
    
    return $group;
  }
}
