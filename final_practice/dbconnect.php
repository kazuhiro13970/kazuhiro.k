<?php

$host     = 'localhost';
$username = 'kazuhiro13970';   // MySQLのユーザ名
$password = '';   // MySQLのパスワード
$dbname   = 'original';   // MySQLのDB名
$charset  = 'utf8';   // データベースの文字コード
// MySQL用のDSN文字列
$dsn = 'mysql:dbname='.$dbname.';host='.$host.';charset='.$charset;
// データベースに接続
$dbh = new PDO($dsn, $username, $password);
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

if ($mysqli->connect_error) {
  error_log($mysqli->connect_error);
  exit;
}
?>