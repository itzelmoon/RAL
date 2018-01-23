<?php
$ROOT="../";
include "{$ROOT}includes/main.php";
include "{$ROOT}includes/post.php";
include "{$ROOT}includes/fetch.php";

header("Content-type: text/xml");
$CONFIG_WEBROOT = CONFIG_WEBROOT;
$CONFIG_ADMIN_MAIL = CONFIG_ADMIN_MAIL;
$posts = fetch_recent_posts(20);

print
<<<XML_HEAD
<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0">
	<channel>
		<title>RAL Neoforum Textboard</title>
		<description>The world's first
		and last Neoforum / Textboard.
		Experience the VIRTUAL WORLD today</description>
		<link>$CONFIG_WEBROOT</link>
		<lastBuildDate>{$posts[0]->date}</lastBuildDate>
		<generator>RAL</generator>

XML_HEAD;

foreach ($posts as $post) {
	$post->content = toHtml($post->content);
	$title = "New Post on [$post->continuity]";
	print
<<<ITEM
		<item>
			<title><![CDATA[$title]]></title>
			<link>$post->url</link>
			<guid isPermaLink="true">$post->url</guid>
			<description><![CDATA[$post->content]]></description>
			<pubDate>$post->date</pubDate>
		</item>

ITEM;
}

print
<<<XML_DONE
	</channel>
</rss>
XML_DONE;
