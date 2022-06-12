<?php

namespace Acms\Plugins\Penne;

use App;
use ACMS_App;
use Acms\Services\Common\HookFactory;
use Acms\Services\Common\ValidatorFactory;
use Acms\Services\Common\InjectTemplate;
use DB;
use SQL;
use Field;
use Acms\Services\Facades\Storage;
use Acms\Services\Facades\Config;

class ServiceProvider extends ACMS_App
{
    /**
     * @var string
     */
    public $version = '1.0.0';

    /**
     * @var string
     */
    public $name = 'Penne';

    /**
     * @var string
     */
    public $author = 'Mr. Gradation';

    /**
     * @var bool
     */
    public $module = false;

    /**
     * @var bool|string
     */
    public $menu = 'penne_index';

    /**
     * @var string
     */
    public $desc = 'カスタムフィールドを管理画面上で設定します。';

    /**
     * データベースの設定
     */
    protected $installTable = array(
        'penne_group',
        'penne_field',
    );

    protected $sequence_key = array(
        'sequence_penne_group_id',
        'sequence_penne_field_id',
    );

    /**
     * サービスの初期処理
     */
    public function init()
    {
        $hook = HookFactory::singleton();
        $hook->attach('PenneHook', new Hook);

        // $validator = ValidatorFactory::singleton();
        // $validator->attach('PenneValidator', new Validator);

        $engine = new Engine();
        App::singleton('penne.engine', function () use ($engine) {
            return $engine;
        });

        // アプリ管理画面を作成
        if (ADMIN === 'app_penne_index') {
            $inect = InjectTemplate::singleton();
            $inect->add('admin-main', PLUGIN_DIR . 'Penne/template/index.html');
            $inect->add('admin-topicpath', PLUGIN_DIR . 'Penne/template/topicpath.html');
        }
        if (ADMIN === 'app_penne_editor') {
            $inect = InjectTemplate::singleton();
            $inect->add('admin-main', PLUGIN_DIR . 'Penne/template/editor.html');
            $inect->add('admin-topicpath', PLUGIN_DIR . 'Penne/template/topicpath.html');
        }
        
        // 各管理画面に挿入
        $inject = InjectTemplate::singleton();
        $inject->add('admin-blog-field', PLUGIN_DIR . 'Penne/theme/field_blog.html');
        $inject->add('admin-category-field', PLUGIN_DIR . 'Penne/theme/field_category.html');
        $inject->add('admin-entry-field', PLUGIN_DIR . 'Penne/theme/field_entry.html');
        $inject->add('admin-module-config-module', PLUGIN_DIR . 'Penne/theme/field_module.html');
        $inject->add('admin-user-field', PLUGIN_DIR . 'Penne/theme/field_user.html');
    }

    /**
     * インストールする前の環境チェック処理
     *
     * @return bool
     */
    public function checkRequirements()
    {
        return true;
    }

    /**
     * インストールするときの処理
     * データベーステーブルの初期化など
     *
     * @return void
     */
    public function install()
    {
        //------------
        //テーブル削除
        dbDropTables($this->installTable);

        //---------------------
        // テーブルデータ読み込み
        $yamlTable = preg_replace('/%{PREFIX}/', DB_PREFIX,
            Storage::get(dirname(__FILE__) . '/schema/db-schema.yaml'));
        $tablesData = Config::yamlParse($yamlTable);
        if (!is_array($tablesData)) {
            $tablesData = array();
        }
        if (!empty($tablesData[0])) {
            unset($tablesData[0]);
        }
        $tableList = array_merge(array_diff(array_keys($tablesData), array('')));

        $yamlIndex = preg_replace('/%{PREFIX}/', DB_PREFIX,
            Storage::get(dirname(__FILE__) . '/schema/db-index.yaml'));
        $indexData = Config::yamlParse($yamlIndex);
        if (!is_array($indexData)) {
            $indexData = array();
        }
        if (!empty($indexData[0])) {
            unset($indexData[0]);
        }

        //---------------
        // テーブル作成
        foreach ($tableList as $tb) {
            $index = isset($indexData[$tb]) ? $indexData[$tb] : null;
            dbCreateTables($tb, $tablesData[$tb], $index);
        }

        //---------------
        // 初期データ生成
        $DB = DB::singleton(dsn());
        foreach ( $this->sequence_key as $key ) {
            $SQL = SQL::newInsert('sequence_plugin');
            $SQL->addInsert('sequence_plugin_key', $key);
            $SQL->addInsert('sequence_plugin_value', 1);
            $DB->query($SQL->get(dsn()), 'exec');
        }
    }

    /**
     * アンインストールするときの処理
     * データベーステーブルの始末など
     *
     * @return void
     */
    public function uninstall()
    {
        dbDropTables($this->installTable);
        
        $DB = DB::singleton(dsn());
        foreach ( $this->sequence_key as $key ) {
            $SQL    = SQL::newDelete('sequence_plugin');
            $SQL->addWhereOpr('sequence_plugin_key', $key);
            $DB->query($SQL->get(dsn()), 'exec');
        }
    }

    /**
     * アップデートするときの処理
     *
     * @return bool
     */
    public function update()
    {
        return true;
    }

    /**
     * 有効化するときの処理
     *
     * @return bool
     */
    public function activate()
    {
        return true;
    }

    /**
     * 無効化するときの処理
     *
     * @return bool
     */
    public function deactivate()
    {
        return true;
    }
}
