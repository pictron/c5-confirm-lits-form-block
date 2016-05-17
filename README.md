# c5-confirm-lits-form-block
concrete5のフォームで確認画面付きでデータベースに保存するブロックベータ

## 設定
$formItemの配列にフォームを設定
$formItem['name'] フォームのname属性
$formItem['validation']に入力条件を配列で設定
$formItem['label'] ラベルを指定
$formItem['type'] フォームのタイプを指定

## ブロックの設定
フォーム名でタイトルを指定
Return mailを「はい」にすると送信者のメールアドレスに返信します。

## インストールディレクトリー

/application/
フォルダのそれぞれ配置