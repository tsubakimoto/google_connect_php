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
	
	// http://ma.snm.dip.jp/php/google_connect_php/redirect.php?state=8741c5192efcf9963f24db1689d38df874ac9a9e&code=4/rduyJaYXt49tEdG9nd6Y4olP8TmE.cgqbF_DCueoUgrKXntQAax3cVNXIfAI
	
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
	
	
	
	// ユーザ情報
	
	// DBへ格納
	
	// ログイン処理
	
	// ホーム画面へ飛ばす
	
}