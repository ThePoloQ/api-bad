<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

include_once(__DIR__.'/classes/rss-club.php');

$feed = new ActuClub();
$posts = $feed->getPosts();

header('Content-type: application/rss+xml;charset=utf-8');
$rss = $feed->toRss($posts);
//echo "<pre>";
echo $rss;
//echo "</pre>";
