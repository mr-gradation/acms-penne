# penne
a-blog cmsのサイトにカスタムフィールドを追加する拡張アプリです。a-blog cmsでカスタムフィールドを追加する際は、通常[カスタムフィールドメーカー](https://developer.a-blogcms.jp/tools/custom-field.html)でカスタムフィールド用の記述を `/admin/*****/field.html` に追加しますが、本拡張アプリを使用することで管理画面から直接追加することができます。追加したカスタムフィールドは他のカスタムフィールドと同じように使用することができます。

## ダウンロード
[acms-penne](https://github.com/mr-gradation/acms-penne/releases/download/v1.0.0/acms-penne-1.0.0.zip)

## インストール
1. config.serve.php を変更し、`define('HOOK_ENABLE', 1);` にしてHOOKを有効にします。
2. ダウンロード後、`extension/plugins/Penne` に設置します。（フォルダ名は１文字目が大文字になります）
3. 管理ページ > 拡張アプリのページに移動し、Penne をインストールします。

## 使い方
1. 管理ページより「Penne」をクリック
![1](https://user-images.githubusercontent.com/15845373/173246155-14ebcf47-db76-41a3-abda-99db77b7b669.png)
2. カスタムフィールドの設定を新規作成
![2](https://user-images.githubusercontent.com/15845373/173246050-36612149-54db-4ec1-b919-da664e5c5d88.png)
3. 設定したカスタムフィールドが反映される
![3](https://user-images.githubusercontent.com/15845373/173246053-47ae2087-dedb-4906-aa26-882567b5ad7e.png)

## 今後追加予定の機能
* カスタムフィールドグループの追加機能
