<?php

// セッション開始
session_start();

// エラーメッセージの初期化
$errorMessage = "";
$signUpMessage = "";

// ログインボタンが押された場合
if (isset($_POST["signUp"])) {
    // 1. ユーザIDの入力チェック
    if (empty($_POST["username"])) {
        $errorMessage = 'ユーザーIDが未入力です。';
    } else if (empty($_POST["pass"])) {
        $errorMessage = 'パスワードが未入力です。';
    } else if (empty($_POST["pass2"])) {
        $errorMessage = 'パスワードが未入力です。';
    }else if($_POST["pass"] != $_POST["pass2"]){
            $errorMessage = 'パスワードに誤りがあります。';
        }else{
            try{

        // 入力したユーザIDとパスワードを格納
        $username = htmlspecialchars($_POST["username"],ENT_QUOTES,'UTF-8');
        $pass = htmlspecialchars($_POST["pass"],ENT_QUOTES,'UTF-8');


        $dsn = '';
        $user='';
        $password='';
        $pass=password_hash($_POST['pass'],PASSWORD_DEFAULT);
        $dbh= new PDO($dsn,$user,$password);
        $sql='INSERT INTO member(name,password) VALUES(:username,:pass)';
        $stmt=$dbh->prepare($sql);
        $stmt->bindParam(':username',$username);
        $stmt->bindParam(':pass',$pass);
        $stmt->execute();
        $dbh=null;
        $signUpMessage=$username.'さん!登録が完了しました!';
            }catch(Exception $e){
            $errorMessage='データベースエラー';
            }

        // 3. エラー処理

    }
}
?>
<!doctype html>
<html>
    <head>
            <meta charset="UTF-8">
            <title>新規登録</title>
    </head>
    <body>
        <h1>新規登録画面</h1>
        <form id="loginForm" name="loginForm" action="" method="POST">
            <fieldset>
                <legend>新規登録フォーム</legend>
                <div><font color="#ff0000"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES); ?></font></div>
                <div><font color="#0000ff"><?php echo htmlspecialchars($signUpMessage, ENT_QUOTES); ?></font></div>
                <label for="username">ユーザー名</label><input type="text" id="username" name="username" placeholder="ユーザー名を入力" value="<?php if (!empty($_POST["username"])) {echo htmlspecialchars($_POST["username"], ENT_QUOTES);} ?>">
                <br>
                <label for="password">パスワード</label><input type="password" id="pass" name="pass" value="" placeholder="パスワードを入力">
                <br>
                <label for="password2">パスワード(確認用)</label><input type="password" id="pass2" name="pass2" value="" placeholder="再度パスワードを入力">
                <br>
                <input type="submit" id="signUp" name="signUp" value="新規登録">
            </fieldset>
        </form>
        <br>
        <form action="Login.php">
            <input type="submit" value="ログイン画面へ">
        </form>
    </body>
</html>