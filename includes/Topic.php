<?php namespace RAL;
class Topic {
	/* SQL Data */
	public $Id;
	public $Created;
	public $Continuity;
	public $Content;
	public $Replies;
	public $Year;

	public function __construct($row) {
		$this->Id = $row['Id'];
		$this->Created = $row['Created'];
		$this->Continuity = $row['Continuity'];
		$this->Content = $row['Content'];
		$this->Replies = $row['Replies'];
		$this->Year = $row['Year'];
		return $this;
	}
	public function resolve() {
		$WROOT = CONFIG_WEBROOT;
		if (CONFIG_CLEAN_URL) return "{$WROOT}view/"
			. rawurlencode($this->Continuity) . '/'
			. rawurlencode($this->Year) . '/'
			. rawurlencode($this->Id);
		else return "{$WROOT}view.php"
			. "?continuity=" . urlencode($this->Continuity)
			. "&year=" . urlencode($this->Year)
			. "&topic=" . urlencode($this->Id);
	}
	public function render() {
		$bbparser = $GLOBALS['RM']->getbbparser();
		$bbparser->parse(htmlentities($this->Content));
		$href = $this->resolve();
		print <<<HTML
	<article>
		<nav>
			<a href="$href" class=id>[
				$this->Continuity /
				$this->Year /
				$this->Id
			]</a>
			<date>$this->Created</date>
		</nav><hr />
		{$bbparser->getAsHtml()}
	</article>

HTML;
	}
	public function renderSelection($items) {
		print <<<HTML
	<main class=flex>
HTML;
		foreach ($items as $i) $i->render();
		print <<<HTML
	</main>
HTML;
	}
}
