<?php
/*
ログインページ
省略

*/
//セッション開始
if(isset($_SESSION['user_id'])){
    //ログイン済みの場合,ホームページへリダイレクト
    header('Location: session_sample_home.php');
    exit;
}

//Cookie情報からメールアドレスを取得
if(isset($_COOKIE['email'])){
    $email = $_COOKIE['email'];
}else{
    $email = '';
}
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <title>ログイン</title>
        <style>
        input{
            display: block;
            margin-bottom: 10px;
        }
        </style>
    </head>
<body>
    <form action="./session_sample_login.php" method="post">
        <label for="email">メールアドレス</label>
        <input type="text" id="email" name="email" value="<?php print $email; ?>">
        <label for="passwd">パスワード</label>
        <input type="password" id="passwd" name="passwd" value="">
        <input type="submit" value="ログイン">
    </form>
</body>
</html>