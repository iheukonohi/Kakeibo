<?php
// セッション開始
session_start();


// エラーメッセージの初期化
$errorMessage = "";

// ログインボタンが押された場合
if (isset($_POST["login"])) {
    // 1. ユーザIDの入力チェック
    if (empty($_POST["username"])) {  // emptyは値が空のとき
        $errorMessage = 'ユーザーIDが未入力です。';
    } else if (empty($_POST["pass"])) {
        $errorMessage = 'パスワードが未入力です。';
    }else{
        $dsn='';
        $user='';
        $password='';

        try {

            $dbh=new PDO($dsn,$user,$password);
            $sql='SELECT password,id FROM member WHERE name=:name;';
            $sth=$dbh->prepare($sql);
            $sth->bindParam(':name',$_POST['username']);
            $sth->execute();
            $pass=$sth->fetch();
            $_SESSION['id']=$pass['id'];

            if(password_verify($_POST['pass'],$pass['password'])){
                $sql='SELECT id FROM member WHERE name=:name';
                $sth=$dbh->prepare($sql);
                $sth->bindParam(':name',$_POST['username']);
                $sth->execute();
                header("Location:caluculate.php");
                exit();
            }else{
                $errorMessage='ユーザーIDあるいはパスワードに誤りがあります。';
            }
        } catch (PDOException $e) {
            $errorMessage = 'データベースエラー';
        }
    }
}
?>

<!doctype html>
<html>
    <head>
            <meta charset="UTF-8">
            <title>ログイン</title>
    </head>
    <body>
        <h1>ログイン画面</h1>
        <form id="loginForm" name="loginForm" action="" method="POST">
            <fieldset>
                <legend>ログインフォーム</legend>
                <div><font color="#ff0000"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES); ?></font></div>
                <label for="username">ユーザーID</label><input type="text" id="username" name="username" placeholder="ユーザーIDを入力" value="<?php if (!empty($_POST["userid"])) {echo htmlspecialchars($_POST["userid"], ENT_QUOTES);} ?>">
                <br>
                <label for="pass">パスワード</label><input type="password" id="pass" name="pass" value="" placeholder="パスワードを入力">
                <br>
                <input type="submit" id="login" name="login" value="ログイン">
            </fieldset>
        </form>
        <br>
        <form action="registration.php">
            <fieldset>
                <legend>新規登録フォーム</legend>
                <input type="submit" value="新規登録">
            </fieldset>
        </form>
        <br>
        <form action="regi-or-log.php">
        <fieldset>
        <legend>ホーム画面へ</legend>
        <input type="submit" value="戻る";>
        </fieldset>
        </form>
    </body>
</html>