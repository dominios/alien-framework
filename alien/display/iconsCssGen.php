<?php

$dir = "img";
if (getcwd() != 'display') {
    chdir('display');
}

$cssPattern1 = '
span.icon.icon-%%ICONNAME%%, span.icon.icon-%%ICONNAME%%-dark {
    background-image: url(\'img/%%ICONURL%%.png\');
}';

$cssPattern2 = '
span.icon.icon-%%ICONNAME%%-light {
    background-image: url(\'img/white/%%ICONURL%%.png\');
}
';

$ret = '';

if (is_dir($dir)) {
    $dh = opendir($dir);
    if ($dh) {
        while (($file = readdir($dh)) !== false) {
            if (!preg_match('/alien/', $file) && preg_match('/\.png$/', $file)) {
                $urlname = preg_replace('/\.png/', '', $file);
                $iconname = preg_replace('/_/', '-', $urlname); // zmeni _ na -
                $ret .= preg_replace('/%%ICONNAME%%/', $iconname, preg_replace('/%%ICONURL%%/', $urlname, $cssPattern1));
                $ret .= '<br>';
                if (file_exists('./img/white/' . $file)) {
                    $ret .= preg_replace('/%%ICONNAME%%/', $iconname, preg_replace('/%%ICONURL%%/', $urlname, $cssPattern2));
                    $ret .= '<br>';
                }
            }
        }
        closedir($dh);
    }
}

file_put_contents('icons-data.css', str_replace('<br>', '', $ret));

//echo $ret;