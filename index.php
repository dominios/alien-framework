<?php

require_once 'alien/init.php';

$request = preg_replace('/^\/{1}/', '', $_SERVER['REQUEST_URI']);
$requestWords = explode('?', $request, 2);
$seolink = $requestWords[0];
if (!strlen($seolink)) {
    $seolink = 1;
}

$pg = new \Alien\Models\Content\Page($seolink);
$tmpl = $pg->getTemplate(true);
$tmpl->fetchViews();
echo '<pre>';
print_r($tmpl->getBlocks());
echo '</pre>';

//foreach ($tmpl->getBlocks() as $b) {
//    foreach ($b['items'] as $i) {
//        echo $i->renderToString();
//    }
//}

//if (ContentPage::exists($seolink)) {
//    $page = new ContentPage($seolink);
//    $template = $page->getTemplate(true);
//    var_dump($template->getBlocks());
////    include $template->getHtmlUrl();
//}


//die('ALiEN V2 under construction<br><a href="/alien">alien</a>');

//$to = "domgersak@gmail.com";
//$subject = "Hi!";
//$body = "Hi,\n\nHow are you?";
//$headers = "From: admin@alien.sk" . "\r\n";
//if (mail($to, $subject, $body, $headers)) {
//    echo ("Message successfully sent!");
//} else {
//    echo ("Message delivery failed...");
//}
//$microtime = MicroTime(1);
//require_once 'alien/init.php';
//
//$uri=$_SERVER['REQUEST_URI'];
//$uri_arr = explode('/',$uri);
//$get=explode('?',end($uri_arr),2);
//$seolink=$get[0];
//
//if(empty($seolink)){
//    //$PAGE = Alien::getHomePage();
//	$PAGE = new ContentPage(1);
//} else {
//    try {
//        $PAGE=new ContentPage($seolink);
//    } catch(UnknownPageException $ex){
//        header("HTTP/1.1 404 Not Found");
//        include 'alien/error/Error404.html';
//        exit;
//        //$PAGE=new ContentPage(Alien::getParameter("notFoundPage")); // zmenit ptm na ERROR 404..
//    }
//}
//$TEMPLATE=$PAGE->getTemplate();
//$BLOCKS=$TEMPLATE->getTemplateBlocks();
//$ALIENWEB['title']=$PAGE->getTitle();
//$ALIENWEB['description']=$PAGE->getDescription();
//$ALIENWEB['keywords']=$PAGE->getKeywords();
//$ALIENWEB['default-css']=$TEMPLATE->getCssUrl();
//foreach($BLOCKS as $BLOCK){
//    $ALIENWEB[$BLOCK]=$PAGE->getPageRenderedContent($BLOCK);
//}
//require_once 'alien/'.$PAGE->getTemplate()->getHtmlUrl();
//
//Alien::renderDebugWindow($microtime);
