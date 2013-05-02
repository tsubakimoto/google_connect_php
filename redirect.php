<?php

require_once('config.php');
require_once('functions.php');

session_start();

if (empty($_GET['code'])) {
	// 認証前の処理
	
	// CSRF対策
	$_SESSION['state'] = sha1(uniqid(mt_rand(), true));
	
	// 認証ダイアログの作成
	$params = array(
		'client_id' => CLIENT_ID
		, 'redirect_uri' => SITE_URL . 'redirect.php'
		, 'state' => $_SESSION['state']
		, 'approval_prompt' => 'force'
		, 'scope' => 'https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/userinfo.email'
		, 'response_type' => 'code'
	);
	
	// Googleへ飛ばす
	$url = 'https://accounts.google.com/o/oauth2/auth?' . http_build_query($params);
	header('Location: ' . $url);
	exit;
	
} else {
	// 認証後の処理
	
	// CSRF対策でstateのチェック
	if ($_SESSION['state'] != $_GET['state']) {
		echo '不正な処理です！';
		exit;
	}
	
	// アクセストークンを取得
	$params = array(
		'client_id' => CLIENT_ID
		, 'client_secret' => CLIENT_SECRET
		, 'code' => $_GET['code']
		, 'redirect_uri' => SITE_URL . 'redirect.php'
		, 'grant_type' => 'authorization_code'
	);
	$url = 'https://accounts.google.com/o/oauth2/token';
	
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_POST, 1);
	curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params));
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	
	$rs = curl_exec($curl);
	curl_close($curl);
	$json = json_decode($rs);
	
	// ユーザ情報
	$url = 'https://www.googleapis.com/oauth2/v1/userinfo?access_token=' . $json->access_token;
	$me = json_decode(file_get_contents($url));
	
	// DBへ格納
	$dbh = connectDb();
	$sql = 'select * from users where google_user_id = :id limit 1';
	$stmt = $dbh->prepare($sql);
	$stmt->execute(array(':id' => $me->id));
	$user = $stmt->fetch();
	
	if (!$user) {
		$sql = "insert into users 
				(google_user_id, google_email, google_name, google_picture, google_access_token, created, modified) 
				values 
				(:google_user_id, :google_email, :google_name, :google_picture, :google_access_token, now(), now())";
		$stmt = $dbh->prepare($sql);
		$params = array(
			":google_user_id" => $me->id, 
			":google_email" => $me->email, 
			":google_name" => $me->name, 
			":google_picture" => $me->picture, 
			":google_access_token" => $json->access_token        
		);
		$stmt->execute($params);
		
		$myId = $dbh->lastInsertId();
		$sql = "select * from users where id = :id limit 1";
		$stmt = $dbh->prepare($sql);
		$stmt->execute(array(":id" => $myId));
		$user = $stmt->fetch();
	}
	
	// ログイン処理
	if (!empty($user)) {
		session_regenerate_id(true);
		$_SESSION['me'] = $user;
	}
	
	// ホーム画面へ飛ばす
	header('Location: ' . SITE_URL);
	exit;
	
}