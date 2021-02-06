<?php
session_start();
//ログイン状態チェック
if (!isset($_SESSION["id"])) {
    header("Location: Logout.php");
    exit;
}
//識別用idの初期化
$id=intval($_SESSION["id"]);
//エラーメッセージ初期化
$errorMessage=NULL;
//結果の初期化
$result=NULL;
//入力チェック
if(isset($_POST['condition'])){
        //データベース接続
        $dsn='';
        $user='';
        $password='';
        $day=$_POST['day'];
        $inorout=$_POST['inorout'];
        $incategory=$_POST['incategory'];
        $outcategory=$_POST['outcategory'];
        //入力チェック
        if($inorout=="---"){
            $errorMessage='収入か支出かを選択してください。';
        }elseif(($inorout=="収入"&&$outcategory!="---")||($inorout=="支出"&&$incategory!="---")){
            $errorMessage='収入(支出)のとき支出(収入)カテゴリーは指定できません';
        }else{
            //入力に応じて条件分岐
            try{
                $dbh=new PDO($dsn,$user,$password);
                //日付のみ入力されている場合
                if($day===""&&$inorout=="収入"&&$incategory=="---"){
                    $sql="SELECT day,income,incategory FROM data WHERE id=:id AND income IS NOT NULL";
                    $stmt=$dbh->prepare($sql);
                    $stmt->bindParam(':id',$id);
                }elseif($day===""&&$inorout=="収入"&&$incategory!="---"){
                    //日付とカテゴリーが入力されている場合
                    $sql='SELECT day,income,incategory FROM data WHERE id=:id AND incategory=:incategory';
                    $stmt=$dbh->prepare($sql);
                    $stmt->bindParam(':id',$id);
                    $stmt->bindParam(':incategory',$incategory);
                }elseif($day===""&&$inorout=="支出"&&$outcategory=="---"){
                    //支出のみ入力されている場合
                    $sql="SELECT day,outcome,outcategory FROM data WHERE id=:id AND outcome IS NOT NULL";
                    $stmt=$dbh->prepare($sql);
                    $stmt->bindParam(':id',$id);
                }elseif($day===""&&$inorout=="支出"&&$outcategory!="---"){
                    //支出とカテゴリーが入力されている場合
                    $sql="SELECT day,outcome,outcategory FROM data WHERE id=:id AND outcategory=:outcategory";
                    $stmt=$dbh->prepare($sql);
                    $stmt->bindParam(':id',$id);
                    $stmt->bindParam(':outcategory',$outcategory);
                }elseif($day!==""&&$inorout=="収入"&&$incategory=="---"){
                    //日付と収入が入力されている場合
                    $sql="SELECT day,income,incategory FROM data WHERE id=:id AND day=:day";
                    $stmt=$dbh->prepare($sql);
                    $stmt->bindParam(':id',$id);
                    $stmt->bindParam(':day',$day);
                }elseif($day!==""&&$inorout=="収入"&&$incategory!="---"){
                    //日付と収入とカテゴリーが入力されている場合
                    $sql='SELECT day,income,incategory FROM data WHERE id=:id AND day=:day AND incategory=:incategory';
                    $stmt=$dbh->prepare($sql);
                    $stmt->bindParam(':id',$id);
                    $stmt->bindParam(':day',$day);
                    $stmt->bindParam(':incategory',$incategory);
                }elseif($day!==""&&$inorout=="支出"&&$outcategory=="---"){
                    //日付と支出が入力されている場合
                    $sql="SELECT day,outcome,outcategory FROM data WHERE id=:id AND day=:day";
                    $stmt=$dbh->prepare($sql);
                    $stmt->bindParam(':id',$id);
                    $stmt->bindParam(':day',$day);
                }elseif($day!==""&&$inorout=="支出"&&$outcategory!="---"){
                    //日付と支出とカテゴリーが入力されている場合
                    $sql="SELECT day,outcome,outcategory FROM data WHERE id=:id AND day=:day AND outcategory=:outcategory";
                    $stmt=$dbh->prepare($sql);
                    $stmt->bindParam(':id',$id);
                    $stmt->bindParam(':day',$day);
                    $stmt->bindParam(':outcategory',$outcategory);
        }
            //SQL文の実行
            $stmt->execute();
            //結果を$resultに代入
            $result=$stmt->fetchAll();


        }catch(Exception $e){
            $errorMessage='履歴がございません。';
        }
    }
}

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>履歴ページ</title>
</head>

<body>
    <div>
        <h1>履歴の表示しましょう</h1>
    </div>
    <div>
        <h2>条件指定</h2>
    </div>
    <div>
        <form action="" method="POST">
            <p>日付:<input type="date" name="day"></p><br>
            <p>収入or支出:<select name="inorout">
                    <option value="---">選択してください</option>
                    <option value="収入">収入</option>
                    <option value="支出">支出</option>
                </select></p>

            <p>収入カテゴリー:<select name="incategory">
                    <option value="---">指定しない</option>
                    <option value="給料">給料</option>
                    <option value="おこづかい">おこづかい</option>
                    <option value="賞与">賞与</option>
                    <option value="副業">副業</option>
                    <option value="投資">投資</option>
                    <option value="臨時収入">臨時収入</option>
                    <option value="その他">その他</option>
                </select></p>
            <p>支出カテゴリー:<select name="outcategory">
                    <option value="---">指定しない</option>
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
                <input type="submit" id="condition" name="condition" value="検索">
        </form>
    </div>
    <div>
    <fieldset><legend>履歴</legend>
<?php
if(isset($_POST['inorout'])){
    //エラーメッセージが代入されていたら表示する
    if(isset($errorMessage)){
    echo $errorMessage;
    }else{
        //収入の履歴を表示
        if($result!=NULL){
            //収入が選択されているとき
            if($_POST['inorout']=="収入"){
                foreach($result as $row){
                    //データベースに収入とカテゴリーが存在しない場合次の処理へ移行する
                    if($row['income']==NULL&&$row['incategory']==NULL){
                    continue;
                    }
                    //日付:  収入:  円カテゴリー:   の順番で表示
                    echo '日付:'.$row['day'].'収入:'.$row['income'].'円カテゴリー:'.$row['incategory'];
                    echo '<br><br>';
                }
            //支出がせんたくされているとき
            }elseif($_POST['inorout']=="支出"){
                foreach($result as $row){
                    //データベースに収入とカテゴリーが存在しない場合次の処理へ移行する
                    if($row['outcome']==NULL&&$row['outcategory']==NULL){
                        continue;
                }
                //日付:  支出:  円カテゴリー:   の順番で表示
                echo '日付:'.$row['day'].'支出:'.$row['outcome'].'円カテゴリー:'.$row['outcategory'];
                echo '<br><br>';
                }
            }
        }else{echo '履歴がございません';}
    }
}
?>
    </fieldset>
    </div><br>
    <div>
    <p>計算ページは<a href="caluculate.php">こちら!</a></p>
    </div><br>
    <a href="logout.php">ログアウト</a>


</body>

</html>