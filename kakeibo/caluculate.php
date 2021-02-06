<?php
session_start();
// ログイン状態チェック
if (!isset($_SESSION["id"])) {
    header("Location: Logout.php");
    exit;
}
//識別用idの初期化
$id=intval($_SESSION['id']);
//エラーメッセージ初期化
$inerrorMessage='';
$outerrorMessage='';
$sumerrorMessage='';
//データベース接続
$dsn='';
$user='';
$password='';
//収入情報をデータベースに代入
//入力チェック
if(isset($_POST['incaluculate'])){
    $income=htmlspecialchars($_POST['income']);
    if(!isset($income)){
    $inerrorMessage='数値を入力してください(少数は入力できません)';
}elseif($income<1){
    $inerrorMessage='0以上の値を入力してください';
}elseif($_POST['incategory']==='---'){
    $inerrorMessage='カテゴリーを入力してください';
}elseif(""===$_POST['inday']){
    $inerrorMessage='日付を入力してください';
}else{
//日付と収入と収入カテゴリーをデータベースに代入
    try{
        $inday=$_POST['inday'];
        $incategory=$_POST['incategory'];
        $dbh=new PDO($dsn,$user,$password);
        $sql="INSERT INTO data(id,day,income,incategory) VALUES (:id,:day,:income,:incategory)";
        $stmt=$dbh->prepare($sql);
        $stmt->bindParam(':day',$inday);
        $stmt->bindParam(':id',$id);
        $stmt->bindParam(':income',$income,PDO::PARAM_INT);
        $stmt->bindParam(':incategory',$incategory);
        $stmt->execute();
    }catch(Exception $e){
        $inerrorMessage='データベースエラー';
    }
    }
}
//支出情報をデータベースに代入
//入力チェック
if(isset($_POST['outcaluculate'])){
    $outcome=htmlspecialchars($_POST['outcome']);
    if(!isset($outcome)){
    $outerrorMessage='数値を入力してください(少数は入力できません)';
}elseif($outcome<1){
    $outerrorMessage='0以上の値を入力してください';
}elseif($_POST['outcategory']==='---'){
    $outerrorMessage='カテゴリーを入力してください';
}elseif(""===$_POST['outday']){
    $outerrorMessage='日付を入力してください';
}else{
    //日付と支出と支出カテゴリーをデータベースに代入
    try{
        $outday=$_POST['outday'];
        $outcategory=$_POST['outcategory'];
        $dbh=new PDO($dsn,$user,$password);
        $sql="INSERT INTO data(id,day,outcome,outcategory) VALUES (:id,:day,:outcome,:outcategory)";
        $stmt=$dbh->prepare($sql);
        $stmt->bindParam(':day',$outday);
        $stmt->bindParam(':id',$id);
        $stmt->bindParam(':outcome',$outcome,PDO::PARAM_INT);
        $stmt->bindParam(':outcategory',$outcategory);
        $stmt->execute();
    }catch(Exception $e){
        $outerrorMessage='データベースエラー';
    }
    }
}
//合計金額を計算
try{
    $dbh=new PDO($dsn,$user,$password);
    $sql="SELECT SUM(income)-SUM(outcome) AS all_sum FROM data WHERE id=:id";
        $stmt=$dbh->prepare($sql);
        $stmt->bindParam(':id',$id);
        $stmt->execute();
        while($row=$stmt->fetch()){
            $caluculatedMessage=$row['all_sum'];
            intval($caluculatedMessage);
        }
}catch(Exception $e){
    $caluculatedMessage='?';
}



?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="utf-8">
    <title>計算ページ</title>
</head>

<body>
    <h1>収入と支出を計算しましょう</h1>
    <div>
        <h2>収入欄</h2>
        <form action="" method="POST">
            <p>日付:<input type="date" name="inday"></p><br>
            <p>収入:<input type="text" name="income">円</p><br>
            <p>カテゴリー:<select name="incategory">
                    <option value="---">以下から選択してください</option>
                    <option value="給料">給料</option>
                    <option value="おこづかい">おこづかい</option>
                    <option value="賞与">賞与</option>
                    <option value="副業">副業</option>
                    <option value="投資">投資</option>
                    <option value="臨時収入">臨時収入</option>
                    <option value="その他">その他</option>
                </select></p>
                <input type="submit" id="incaluculate" name="incaluculate" value="計算">
                <font color="#ff0000"><?php echo htmlspecialchars($inerrorMessage, ENT_QUOTES); ?></font>

    </div>
    <div>
        <h2>支出欄</h2>
        <form action="" method="POST">
            <p>日付:<input type="date" name="outday" ></p><br>
            <p>支出:<input type="text" name="outcome">円</p><br>
            <p>カテゴリー:<select name="outcategory">
                    <option value="---">以下から選択してください</option>
                    <option value="食費">食費</option>
                    <option value="日用品">日用品</option>
                    <option value="衣服">衣服</option>
                    <option value="娯楽">娯楽</option>
                    <option value="医療費">医療費</option>
                    <option value="臨時収入">臨時収入</option>
                    <option value="光熱費">光熱費</option>
                    <option value="交通費">交通費</option>
                    <option value="その他">その他</option>
                </select></p>
                <input type="submit" id="outcaluculate" name="outcaluculate" value="計算">
                <font color="#ff0000"><?php echo htmlspecialchars($outerrorMessage, ENT_QUOTES); ?></font>
                </div>
    <div>
        <h2>合計金額</h2>
        <p><?php if(isset($caluculatedMessage)){echo '合計金額は'.$caluculatedMessage.'円です。';} ?></p>
    </div><br>
    <div>
    履歴ページは<a href="history.php">こちら!</a>
    </div><br>
    <a href="logout.php">ログアウト</a>

</body>

</html>