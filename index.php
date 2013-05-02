<?php

require_once('config.php');
require_once('functions.php');

session_start();

if (empty($_SESSION['me'])) {
	header('Location: ' . SITE_URL . 'login.php');
	exit;
}

?>
<!doctype html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<title>Home : Google connect php</title>
</head>
<body>
	<h1>ホーム画面</h1>
	<p>
		<?php echo h($_SESSION['me']['google_name']); ?>
		(<?php echo h($_SESSION['me']['google_email']); ?>)
		のGoogleアカウントでログインしています。
	</p>
</body>
</html>