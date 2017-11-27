<!DOCTYPE HTML>
<HTML>
<head>
	<link rel=stylesheet href="css/base.css">
	<link rel=stylesheet href="css/20XX.css">
	<title>RAL</title>
</head>
<body>
<div id=timelines class='frontcenter'>
	<h2>Connected</h2>
	<span>128 ms delay</span>
	<div class=collection id=test>
	</div>
	<nav id=timelinenav>
	<a class='leftnav' onClick='window.transitions.newpage(
		document.getElementById("test")
	)'>◀</a>
	<a class='rightnav' onClick='window.transitions.newpage(
		document.getElementById("test")
	)'>▶</a>
	</nav>
</div>
<div id=sakura>
	<video autoplay loop>
		<source src='res/splash.webm'>
	</video>
	<img src='res/fallback.gif'>
</div>
</body>
<script src='js/esthetic.js'></script>
<script src='js/transitions.js'></script>
<script src='js/remote.js'></script>
<script>
	window.transitions.newpage(
		document.getElementById('test'),
		0
	);
</script>
</HTML>
