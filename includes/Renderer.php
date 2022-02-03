<?php namespace RAL;
include "{$ROOT}includes/Theme.php";
class Renderer {
	public $Title;
	public $Desc;
	public $Theme;
	public $ShowComposer = false;
	public $PendingMessage;
/*	public $Language;*/

	private $rm;

	public function __construct($rm) { $this->rm = $rm; }
	public function setTheme($name = null) {
		$this->Theme = new Theme($name);
	}
/*	public function setLanguage($language) {
		$this->Language = $language;
	}*/
	public function themeFromCookie($cookie) {
		$theme = $cookie['Theme'];
		if ($theme && in_array($theme, CONFIG_THEMES))
			$this->setTheme($theme);
		else
			$this->setTheme(CONFIG_DEFAULT_THEME);
	}

	/* Rendering the object's banner */
	public function PutBanner($resource) {
		$href = $resource->resolve();
		$src = $resource->BannerURL();
		print <<<HTML
	<div class=banner><a href="$href">
		<img height=150 width=380
		src="$src"/>
	</a></div>

HTML;
	}

	public function configForm() {
		print <<<HTML
<form method=POST>
<fieldset><legend>Site Theme</legend>
HTML;
		foreach (CONFIG_THEMES as $theme) {
			$q_theme = htmlentities($theme, ENT_QUOTES);
			$h_theme = htmlentities($theme);
			if (!strcmp($theme, $this->Theme->name)) print <<<HTML
	<input type=radio name=Theme id="Theme-$q_theme" value="$q_theme" checked>
	<label for="Theme-$q_theme">$h_theme</label><br />
HTML;
			else print <<<HTML
	<input type=radio name=Theme id="Theme-$q_theme" value="$q_theme">
	<label for="Theme-$q_theme">$h_theme</label>
HTML;
		}
/*		print <<<HTML
	<h2>Language</h2>
HTML;
		foreach (CONFIG_LANGS as $lang) {
			$q_lang = htmlentities($lang, ENT_QUOTES);
			$h_lang = htmlentities($lang);
			if (!strcmp($lang, $this->Language)) print <<<HTML
	<input type=radio name=Language id=Language-$q_lang
	value=$q_lang checked>
	<label for=Language-$q_lang>$h_lang</label>
HTML;
			else print <<<HTML
	<input type=radio name=Language id=Language-$q_lang
	value=$q_lang>
	<label for=Language-$q_lang>$h_lang</label>
HTML;
		}*/
		print <<<HTML
	</fieldset><br /><input class=button type=submit value=Submit>
</form>
HTML;
	}

	public function putHead() {
	$WROOT = CONFIG_WEBROOT;
	$LOCALROOT = CONFIG_LOCALROOT;
	@$themefile = $this->Theme->css();
	print
<<<HTML
	<meta name=viewport content="width=device-width,
	maximum-scale=1, minimum-scale=1">
	<link rel=stylesheet href="${WROOT}css/Base.css">
	<link rel=icon type="image/x-icon" href="${WROOT}favicon.gif">

HTML;
	if (@$themefile) {
		print <<<HTML
	<link rel=stylesheet href="$themefile">

HTML;
	} if (isset($this->Title)) {
		$title = htmlspecialchars($this->Title);
		print <<<HTML
	<title>$title - RAL Neo-Forum Textboard</title>

HTML;
	} if (isset($this->Desc)) {
		$desc = htmlspecialchars($this->Desc, ENT_QUOTES);
		print <<<HTML
	<meta name=description content="$desc"/>

HTML;
	}
/*	if (@file_exists("${LOCALROOT}www/js/themes/$theme.js")) {
		print <<<HTML
	<script src="${WROOT}js/themes/$theme.js"></script>

HTML;*/
	}

	function asHtml($content) {
		return $this->rm->asHtml($content);
	}

	/* Render the object with the specificed format */
	public function Put($r, $format) {
		if ($r and is_array($r)) switch($r[0]->Type()) {
			case "News": $this->News($r, $format); break;
			case "Recent": $this->Recent($r, $format); break;
			case "Topic": $this->Topics($r, $format); break;
			case "Continuity": $this->Continuities($r, $format); break;
			case "Year": $this->Years($r, $format); break;
			case "Reply": $this->Replies($r, $format); break;
		} else if ($r) switch ($r->Type()) {
			case "Topic": $this->Topic($r, $format); break;
			case "Continuity": $this->Continuity($r, $format); break;
			case "Year": $this->Year($r, $format); break;
			case "Reply": $this->Reply($r, $format); break;
	} }

	public function News($r, $format) { switch($format) {
		case "html":
			print <<<HTML
		<article><h2>News</h2>
HTML;
			$this->NewsSlice($r, $format);
			print <<<HTML
		</article>
HTML;
	} }

	public function NewsSlice($slice, $format) { switch($format) {
		case "html":
			foreach ($slice as $newspost) {
				$id = $newspost->Id;
				$time = strtotime($newspost->Created);
				$prettydate = date('l M jS \'y', $time);
				$datetime = date(DATE_W3C, $time);
				$author = $newspost->Author;
				$email = $newspost->Email;
				$title = $newspost->Title;
				$content = $this->asHtml($newspost->Content);

				print <<<HTML
				<section class=news-item>
					<h3 class=title>$title</h3>
					by <a href="mailto:$email">$author</a>
				/ <time datetime="$datetime">$prettydate</time><hr />
				$content
				</section>
HTML;
			}
	} }

	public function Year($r, $format) { switch($format) {
		case "json":
			print json_encode($r->Children());
		break;
		case "html":
			if ($this->ShowComposer) { $this->NewTopic($r); }
			else if ($this->PendingMessage) {
				$this->PreviewTopic(new PreviewTopic([
					"content" => $this->PendingMessage,
					"continuity" => $r->Name]));
			}
			$topics = $r->Children();
			print <<<HTML
	<article>
	<h2>{$r->title()}</h2>	<nav class=info-links>
		<a class=button href="{$r->resolveComposer()}">Inicia un conversación/ Hilo</a>
	</nav>

HTML;
			// TODO: Post Button
				print <<<HTML
	<div class=content>

HTML;
			$this->YearSlice($topics, $format);
				print <<<HTML
	</div>

HTML;
				print <<<HTML
	</article>

HTML;
	} }

	public function Continuities($slice, $format) { switch($format) {
		case "json":
			print json_encode($slice);
		break;
		case "html":
			print <<<HTML
<article>
<h2>Continuities</h2><div class=continuity-splashes>

HTML;

		$this->ContinuitiesSlice($slice, "html");
			print <<<HTML
</div></article>

HTML;
	} }

	public function NewTopic($r) {
		$WROOT = CONFIG_WEBROOT;
		$action = htmlentities($r->resolve());
		$cancel = htmlentities($r->resolve());
		$minlength = CONFIG_MIN_POST_BYTES;
		if (CONFIG_CLEAN_URL) $bbcoderef = "{$WROOT}bbcode-help";
		else $bbcoderef = "{$WROOT}bbcode-help.php";
		print <<<HTML
<article class=composer>
		<h2>Nuevo hilo en on {$r->title()}</h2>
		<form method=POST action="$action" class=composer>
		<div class=textarea>
			<textarea autofocus rows=5 tabindex=1
			maxlength=5000 minlength=$minlength
			placeholder="Contribute your thoughts and desires..."
			name=content>$content</textarea>
		</div>
		<div class=buttons>
			<a href="$cancel" class="cancel button">Cancelar</a>
			<button value=preview name=preview
			tabindex=2 class=button
			type=submit>Siguiente</button>
			<a href="$bbcoderef">Using BBCode</a>
		</div>
		</form>
</article>
HTML;
	}

	public function PreviewReply($p) {
		print <<<HTML
		<h2>Double Check</h2>
		<p>Antes de postear, verifica que todo está como lo deseas enviar.
			Si la vista previa se ve bien, continúa al test antibot 
			y enviar tu publicación.</p>

HTML;

		$this->TopicSlice([$p], "html");
		$encodedContent = htmlspecialchars($p->Content);
		print <<<HTML
		<form method=POST class=composer>
		<input type=hidden name=content value="$encodedContent">
		<input name=robocheck id=robocheck type=checkbox>
		<label for=robocheck>I am not a robot</label>
		<div class="buttons">
			<a href="$cancel" class="cancel button">Cancel</a>
			<button name=post value=post type=submit
			tabindex=2>Post</button>
		</div>
		<input id=robocheck-fail class="only dumb robots sit in this
		box" name=robocheck-fail type=checkbox>
		<label class="only dumb robots sit in this box"
		for=robocheck>Only dumb robots fill out this box</label>
		</form>

HTML;
	}

	public function PreviewTopic($t) {
		print <<<HTML
		<h2>Double Check</h2>
		<p>Antes de postear, verifica que todo está como lo deseas enviar.
			Si la vista previa se ve bien, continúa al test antibot 
			y enviar tu publicación.</p>

HTML;
		$encodedContent = htmlspecialchars($t->Content);
		$this->YearSlice([$t], "html");
		print <<<HTML
		<form method=POST class=composer>
		<input type=hidden name=content value="$encodedContent">
		<input name=robocheck id=robocheck type=checkbox>
		<label for=robocheck>No soy un robot</label>
		<div class="buttons">
			<a href="$cancel" class="cancel button">Cancelar</a>
			<button name=post value=post type=submit
			tabindex=2>Post</button>
		</div>
		<input id=robocheck-fail class="only dumb robots sit in this
		box" name=robocheck-fail type=checkbox>
		<label class="only dumb robots sit in this box"
		for=robocheck>Solo los robots tontos llenan este cuadro</label>
		</form>

HTML;
	}

	public function NewReply($r) {
		$WROOT = CONFIG_WEBROOT;
		$action = htmlentities($r->resolve());
		$cancel = htmlentities($r->resolve());
		$minlength = CONFIG_MIN_POST_BYTES;
		if (CONFIG_CLEAN_URL) $bbcoderef = "{$WROOT}bbcode-help";
		else $bbcoderef = "{$WROOT}bbcode-help.php";
		print <<<HTML
		<h2>Responder a {$r->title()}</h2>
		<form method=POST action="$action" class=composer>
		<div class=textarea>
			<textarea autofocus rows=5 tabindex=1
			maxlength=5000 minlength=$minlength
			placeholder="Contribute your thoughts and desires..."
			name=content>$content</textarea>
		</div>
		<div class=buttons>
			<a href="$cancel" class="cancel button">Cancelar</a>
			<button value=preview name=preview
			tabindex=2 class=button
			type=submit>Enviar</button>
			<a href="$bbcoderef">Usando BBCode</a>
		</div>
		</form>

HTML;
	}

	public function ContinuitiesSlice($slice, $format) { switch($format) {
		case "html":
			foreach ($slice as $continuity) {
			$href = $continuity->resolve();
			$src = $continuity->BannerURL();
			$alt = $continuity->Name;
			$desc = $continuity->Description;
			$title = $continuity->Title();

			print <<<HTML
	<section class=continuity-splash>
		<div class=banner><a href="$href">
			<img height=150 width=380
			title="$title" alt="$alt"
			src="$src" />
		</a></div>
		<h2 class=title>
			<a href="$href">$title</a>
		</h2>
		<span class=description>$desc</span>
	</section>

HTML;
		}
	} }

	public function Continuity($r, $format) { switch($format) {
		case "json":
			print json_encode($r->Children());
			break;
		case "html":
			if ($this->ShowComposer) { $this->NewTopic($r); }
			else if ($this->PendingMessage) {
				$this->PreviewTopic(new PreviewTopic([
					"content" => $this->PendingMessage,
					"continuity" => $r->Name]));
			}

			print <<<HTML
<article class=timeline>
	<h2>Overview of {$r->Title()}</h2><ul>

HTML;
		$this->ContinuitySlice($r->Children(), $format);
			print <<<HTML
</ul></article>

HTML;
	} }

	public function YearSlice($slice, $format) { switch($format) {
		case "html":
			foreach ($slice as $topic) {
			$time = strtotime($topic->Created);
			$prettydate = date('l M jS \'y', $time);
			$datetime = date(DATE_W3C, $time);
		print <<<HTML
		<section class=post>
		<header>
			<div><h3 class=id>{$topic->title()}</h3>

HTML;
		if ($topic->resolve()) {
			$href = htmlentities($topic->resolve());
			print <<<HTML
			<span class=expand><a href="$href">Leer Hilo </a></span>

HTML;
		} else { print <<<HTML
			<span class=expand>Hilo anterior</span>
HTML;
		} print <<<HTML
			</div>
		<ul class=attrs>
		<li><time datetime="$datetime">$prettydate</time></li>
		<li>{$topic->Replies} Respuestas</li>
		</ul>
</header><hr />
HTML;
					print $this->asHtml($topic->Content);
		print <<<HTML
	</section>

HTML;
			}
	} }

	public function Topic($r, $format) { switch($format) {
		case "json":
			print json_encode($r->Children());
		break;
		case "html":
			if ($this->ShowComposer) { $this->NewReply($r); }
			else if ($this->PendingMessage) {
				$this->PreviewReply(new PreviewReply([
					"content" => $this->PendingMessage,
					"topic" => $r->Topic,
					"year" => $r->Year,
					"continuity" => $r->Continuity]));
			}
			print <<<HTML
<article>
<h2>{$r->title()}</h2>
<nav class=info-links>
	<a class=button href="{$r->resolveComposer()}">Responde este hilo</a>
</nav>

HTML;
		$this->TopicSlice($r->Children(), "html");
		print <<<HTML
</article>
HTML;
	} }

	public function TopicSlice($slice, $format) { switch($format) {
		case "html":
		foreach ($slice as $reply) {
			$time = strtotime($reply->Created);
			$prettydate = date('l M jS \'y', $time);
			$datetime = date(DATE_W3C, $time);
			list($idbg, $idfg) = $this->IdColor($reply->UserIdentity);
		print <<<HTML
		<section class=post id=$reply->Id>
		<header>
			<h3 class=id>{$reply->title()}</h3>
		<ul class=attrs>
			<li>Author: <span class=author
				style="color:$idfg;background-color:$idbg">
				$reply->UserIdentity</span></li>
			<li><time datetime="$datetime">$prettydate</time></li>
		</ul>
</header><hr />

HTML;
		print $this->asHtml($reply->Content);
		print <<<HTML
</section>

HTML;
		}
	} }

	public function Recent($r, $format) { switch($format) {
		case "html":
			print <<<HTML
<h2>Fresh Posts</h2><article>
HTML;
		$this->RecentSlice($r, "html");
			print <<<HTML
</article>
HTML;
	} }

	public function RecentSlice($slice, $format) {
		foreach ($slice as $recent) { switch($format) {
		case "html":
			$time = strtotime($recent->Created);
			$prettydate = date('l M jS \'y', $time);
			$datetime = date(DATE_W3C, $time);
			list($idbg, $idfg) = $this->IdColor($recent->UserIdentity);
			$href = htmlentities($recent->resolve());
		print <<<HTML
		<section class=post>
		<header>
			<div><h3 class=id>{$recent->title()}</h3>
			<span class=expand><a href="$href">Show Context</a></span></div>
		<ul class=attrs>
			<li>Author: <span class=author
				style="color:$idfg;background-color:$idbg">
				$recent->UserIdentity</span></li>
		<li><time datetime="$datetime">$prettydate</time></li>
		</ul>
</header><hr />

HTML;
		print $this->asHtml($recent->Content);
		print <<<HTML
</section>

HTML;
		break;
		case "rss":
			$content = htmlspecialchars($recent->Content, ENT_COMPAT,'utf-8');
			$url = CONFIG_CANON_URL . htmlentities($recent->resolve());
			$title = $recent->title();
			$date = date(DATE_RSS, strtotime($recent->Created));
			print <<<RSS
<item>
	<title>$title</title>
	<link>$url</link>
	<guid isPermaLink="true">$url</guid>
	<description>$content</description>
	<pubDate>$date</pubDate>
</item>
RSS;
		} }
	}

	public function ContinuitySlice($slice, $format) { switch($format) {
		case "html":
			foreach ($slice as $year) {
			$href = htmlentities($year->resolve());
			print <<<HTML
	<li><div>
		<time><a href="$href">$year->Year</a></time><br />
		$year->Count Posts
	</div></li>

HTML;
			}
	} }

	// Returns a [BG Color, FG Color] for each Id
	public function IdColor($id) {
		$seed = substr(bin2hex(base64_decode($id)), 0, 1);
		switch($seed) {
			case '0': return ['blue', 'white'];
			case '1': return ['black', 'white'];
			case '2': return ['crimson', 'white'];
			case '3': return ['deeppink', 'white'];
			case '4': return ['orchid', 'white'];
			case '5': return ['sienna', 'white'];
			case '6': return ['steelblue', 'white'];
			case '7': return ['darkturquoise', 'black'];
			case '8': return ['fuchsia', 'black'];
			case '9': return ['gold', 'black'];
			case 'a': return ['lightblue', 'black'];
			case 'b': return ['lime', 'black'];
			case 'c': return ['mistyrose', 'black'];
			case 'd': return ['springgreen', 'black'];
			case 'e': return ['violet', 'black'];
			case 'f': return ['violet', 'white'];
		}
	}
}
