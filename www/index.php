<?php
$ROOT = '../';
include "{$ROOT}includes/main.php";
include "{$ROOT}includes/ContinuityIterator.php";
include "{$ROOT}includes/News.php";

$iterator = new RAL\ContinuityIterator();
?>
<!DOCTYPE HTML>
<HTML>
<head>
<?php
	$pagetitle = "Home";
	$pagedesc = "The world's first and last Neo-Forum / Textboard."
	. " Experience the VIRTUAL WORLD today.";
	include "{$ROOT}template/head.php";
?>
	<link rel=alternate type="application/rss+xml" title=RSS
<?php
	if (CONFIG_CLEAN_URL)
		$rss = CONFIG_WEBROOT . "rss";
	else
		$rss = CONFIG_WEBROOT . "rss.php";
	print
<<<HREF
	href="$rss"
HREF;
?>>
</head>
<body>
<header>
	<div>
		<h1 class=glitch data-text=RAL>RAL</h1>
	</div>
	<span>Neo-Forum Textboard</span><br />
</header>
<?php include "{$ROOT}template/Feelies.php" ?><hr />
<?php $iterator->drawRSSButton(); ?>
<?php $iterator->select(); $iterator->render(); ?>
<?php (new RAL\News())->select()->draw(); ?><hr />
<?php include "{$ROOT}info/About.txt" ?><hr />
<?php include "{$ROOT}info/Rules.txt" ?>
<hr /><footer>
<?php include "{$ROOT}template/Footer.php"; ?>
</footer>
</body>
</HTML>