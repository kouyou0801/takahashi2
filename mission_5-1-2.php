<html>
<?php

	$dsn='データベース名';
	$user='ユーザー名';
	$password='パスワード';
	// データベースに接続
	try{
		$dbh=new PDO($dsn,$user,$password);
		
		//mission_4-2から引用
		$sql="CREATE TABLE IF NOT EXISTS mysql2" //新たなテーブル"musq2"を作る
			."("
			."id INT AUTO_INCREMENT PRIMARY KEY,"
			."Name char(32),"
			."Comment TEXT,"
			."Datetime TEXT," //日付が入る
			."Password TEXT" //パスワードが入る
			.");";
		$stmt=$dbh->query($sql); //$sqlの中身を実行
	}
	catch (PDOException $e){ //データベースの接続に失敗した場合
		echo ("接続エラー".$e ->getMessage());
		exit();
	}

	//mission3-5と同じ部分
	$myname=@$_POST["name"];
	$comment=@$_POST["comment"];
	$password=@$_POST["password"];
	$password1=@$_POST["password1"];
	$password2=@$_POST["password2"];
	$delete=@$_POST["delete_num"];
	$del_id=0; //削除で用いる変数で0を代入しておく
	$editor=@$_POST["edit"];
	$hidebefore=@$_POST["hidebefore"];

if(!empty($myname&$comment)&&!empty($_POST["send_button"])){

	//名前とコメントが入力されているときに実行

	if(!empty($password)){ //パスワードも入力されている場合に実行

		if(empty($hidebefore)){//新規送信の時

			//mission_4-5から引用

			$sql=$dbh->prepare("INSERT INTO mysql2 (Name, Comment, Datetime, Password) VALUES (:name, :comment, :datetime, :password)");
			$sql->bindParam(':name', $name_data, PDO::PARAM_STR);
			$sql->bindParam(':comment', $comment_data, PDO::PARAM_STR);
			$sql->bindParam(':datetime', $date, PDO::PARAM_STR);
			$sql->bindParam(':password', $pass_data, PDO::PARAM_STR);
			$name_data=$myname;
			$comment_data=$comment;
			$date=date("Y/m/d H:i:s"); //投稿した日付を変数$dataに代入
			$pass_data=$password; //入力したパスワードを変数$pass_dataに代入
			$sql -> execute(); //$sqlの内容を実行
		}
		else if(!empty($hidebefore)&&!empty($password)){ //編集を行う時の処理
			//パスワードが入力されているときに実行
			
			//mission_4-7から引用

			$name_data2=$myname;
			$comment_data2=$comment;
			$pass_data2=$password;
			$date2=date("Y/m/d H:i:s"); //投稿した日付を変数$data2に代入
			$sql="update mysql2 set Name=:name,Comment=:comment,Password=:password,Datetime=:datetime where id=:id";
			$stmt=$dbh->prepare($sql);
			$stmt->bindParam(':name', $name_data2, PDO::PARAM_STR);
			$stmt->bindParam(':comment', $comment_data2, PDO::PARAM_STR);
			$stmt->bindParam(':password', $pass_data2, PDO::PARAM_STR);
			$stmt->bindParam(':datetime', $date2, PDO::PARAM_STR);
			$stmt->bindParam(':id', $id, PDO::PARAM_INT);
			$id=$hidebefore; //変更する投稿番号
			$stmt->execute(); //$sqlの内容を実行
		}
	} //パスワードが入力されている時の処理

	else{ //パスワードのみ入力されていない場合に実行
		echo "パスワードを入力してください!<br>";
	}
}//名前とコメントとを入力した時の処理

//削除フォーム
if(!empty($delete)&&!empty(@$_POST["delbutton"])){

	//削除番号と削除ボタンが入力された時に実行

	if(!empty($password1)){ //パスワードも入力されている場合に実行
		$sql="SELECT * from mysql2";
		$stmt=$dbh->query($sql);
		$results=$stmt->fetchAll();
		foreach ($results as $row){
			if($delete==$row["id"]&&$password1==$row["Password"]){
				//入力した削除番号とパスワードが一致したときだけ実行する
				$del_id=$row["id"]; //削除したい番号を変数$del_idに代入
			}

			//$del_idにはもともと0が代入されているので,パスワードが一致しない場合は
			//値は0のままである。このときは$idと番号が一致しないので削除されない

			else if($delete==$row["id"]&&$password1!=$row["Password"]){
				//パスワードが一致しない時に実行
				echo "パスワードが正しくありません!<br>";
			}
		} //ループ終了

		//mission_4-8から引用
		$sql="delete from mysql2 where id=:id"; //削除番号と一致する行を削除する
		$stmt=$dbh->prepare($sql);
		$stmt->bindParam(':id', $id, PDO::PARAM_INT);
		$id=$del_id; //削除する投稿番号
		$stmt->execute();
	}

	else{ //パスワードが入力されていない場合に実行
		echo "パスワードを入力してください!<br>";
	}
}//削除を行う時の処理

//編集フォーム
if(!empty($editor)&&isset($_POST["tbutton"])){
	
	//編集対象番号と編集ボタンが入力されている時に実行

	if(!empty($password2)){ //パスワードも入力されている時に実行
		$sql="SELECT * from mysql2";
		$stmt=$dbh->query($sql);
		$results=$stmt->fetchAll();
		foreach ($results as $row){
			if($editor==$row["id"]&&$password2==$row["Password"]){
				//入力した編集番号とパスワードが一致したときだけ実行する
				$e_myname=$row["Name"];
				$e_comment=$row["Comment"];
				$e_password=$row["Password"]; //変数の中身は送信フォームに表示される
			}
			else if($editor==$row["id"]&&$password2!=$row["Password"]){
				//パスワードが一致しない時に実行
				echo "パスワードが正しくありません!<br>";
				$e_myname="";
				$e_comment="";
				$e_password="";
			}
		} //ループ終了
	} //パスワードが入力されている時の処理

	else{ //パスワードが入力されていない時に実行
		echo "パスワードを入力してください!<br>";
		$e_myname="";
		$e_comment="";
		$e_password="";
	}
} //編集を行う時の処理
?>

<form method="POST" action="">
<!--名前入力フォーム-->
<input type="text" name="name" placeholder="名前" value="<?php if(!empty($_POST["edit"])&&isset($_POST["tbutton"])){echo $e_myname;}?>"><br><br>
<!--コメント入力フォーム-->
<input type="text" name="comment" placeholder="コメント" value="<?php if(!empty($_POST["edit"])&&isset($_POST["tbutton"])){echo $e_comment;}?>"><br><br>
<input type="hidden" name="hidebefore" value="<?php if(!empty($_POST["edit"])&&isset($_POST["tbutton"])){echo $editor;}?>">
<input type="text" name="password" placeholder="パスワード" value="<?php if(!empty($_POST["edit"])&&isset($_POST["tbutton"])){echo $e_password;}?>"><br><br><br>
<input type="submit" name="send_button" value="送信">
<br><br>
<!--削除番号指定フォーム-->
<input type="text" name="delete_num" placeholder="削除対象番号"><br><br>
<input type="text" name="password1" placeholder="パスワード確認"><br>
<input type="submit" name="delbutton" value="削除">
<br><br>
<!--編集番号指定フォーム-->
<input type="text" name="edit" placeholder="編集対象番号"><br><br>
<input type="text" name="password2" placeholder="パスワード確認"><br>
<input type="submit" name="tbutton" value="編集">
</form>
</html>

<?php
//データベース表示処理

	//mission_4-6から引用
	$sql="SELECT * FROM mysql2";
	$stmt=$dbh->query($sql);
	$results=$stmt->fetchAll();
	foreach ($results as $row){
		//$rowの中にはテーブルのカラム名が入る
		echo $row["id"].",";
		echo $row["Name"].",";
		echo $row["Comment"].".";
		echo $row["Password"].".";
		echo $row["Datetime"]."<br>";
	echo "<hr>";
	}
?>