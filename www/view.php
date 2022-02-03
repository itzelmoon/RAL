<?php
$ROOT = '../';
include "{$ROOT}includes/main.php";
include "{$ROOT}includes/Ral.php";
include "{$ROOT}includes/Renderer.php";

$rm = new RAL\ResourceManager();
$Renderer = new RAL\Renderer($rm);
$Renderer->themeFromCookie($_COOKIE);
$Ral = new RAL\Ral($rm);

// Which continuity we are reading
$continuity = urldecode($_GET['continuity']);
// Which year are we browsing?
$year = @$_GET['year'];
// Which topic (if any) we are reading
$topic = @$_GET['topic'];
// Which posts (if any) we are reading
$replies = @$_GET['replies'];
// Should we display the post-writer?
$compose = @$_GET['compose'];

if ($topic) $specifies = "topic";
else if ($year) $specifies = "year";
else if ($continuity) $specifies ="continuity";

$resource = $Ral->Select($continuity, $year, $topic, $replies);
if (!$continuity) {
	http_response_code(404);
	include "{$ROOT}template/404.php";
	die;
}

if (isset($compose))
	$Renderer->ShowComposer = true;
else if (@$_POST['preview'])
	$Renderer->PendingMessage = $_POST['content'];
else if (@$_POST['post']) {
	$page = $resource->resolve();
	$until = 3;
	if ($_POST['robocheck-fail']) {
		$reason = "Lo siento, ¡no dejo que los robots publiquen aquí!";
		header($_SERVER["SERVER_PROTOCOL"]." 400 Bad Request"); 
		header("Refresh: $until; url=$page");
		include "{$ROOT}template/PostFailure.php";
		die;
	} else if (!isset($_POST['robocheck'])) {
		$reason = "¿Olvidaste verificar que eras humano?";
		header("Refresh: $until; url=$page");
		include "{$ROOT}template/PostFailure.php";
		die;
	} else if (empty(@$_POST['content'])) {
		$reason = "¿Qué estás tratando de hacer?";
		header("Refresh: $until; url=$page");
		include "{$ROOT}template/PostFailure.php";
		die;
	} else if (strlen($_POST['content']) < CONFIG_MIN_POST_BYTES) {
		$reason = "Tu publicación es muy corta... ¡por favor escribe más!";
		header("Refresh: $until; url=$page");
		include "{$ROOT}template/PostFailure.php";
		die;
	} else if (strlen($_POST['content']) > CONFIG_MAX_POST_BYTES) {
		$reason = "Tu publicación es demasiado larga... ¡sé más breve!";
		header("Refresh: $until; url=$page");
		include "{$ROOT}template/PostFailure.php";
		die;
	}

	$b8 = $rm->getb8();
	$spamminess = $b8->classify($rm->asHtml($_POST['content']));
	if ($spamminess > CONFIG_SPAM_THRESHOLD) {
		$reason = "Mmm... código de error: " . round($spamminess * 100);
		header("Refresh: $until; url=$page");
		include "{$ROOT}template/PostFailure.php";
		die;
	}

	switch ($specifies) {
		case "continuity":
		case "year":
			$Ral->PostTopic($continuity, $_POST['content'], $_COOKIE['id']);
			break;
		case "topic":
			$Ral->PostReply($continuity, $year, $topic, $_POST['content'], $_COOKIE['id']);
	}
	header("Refresh: $until; url=$page");
	include "{$ROOT}template/PostSuccess.php";
	die;
}

?>
<!DOCTYPE HTML>
<HTML>
<head>
<?php
	$Renderer->Title = $resource->Title();
	$Renderer->Desc = $resource->Description();
	$Renderer->putHead();
?>
</head>
<body>
<div><header><?php $Renderer->PutBanner($resource); ?>
<?php include "{$ROOT}template/Feelies.php"; ?></header></div>
<div class=main><main>
<?php $Renderer->Put($resource, "html"); ?>
</main></div>
<div class=discovery>
<?php include "{$ROOT}template/Sponsors.php"; ?>
</div>
<footer>
<?php include "{$ROOT}template/Footer.php"; ?>
</footer>
</body>
</HTML>
