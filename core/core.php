<?php

namespace origamy;

class BeautifyAPI{
    protected $key = 'gMOwURjTHu8DeOclQQ6VQsPEpKNqknpXgYjEaPkWVXpKM';
    
    protected $url = 'https://api.dotmaui.com/client/';
    
    var $output;
    
    function beautifyHTML($html){
        $this->url = $this->url.'1.0/htmlbeautify/';
        $data = array(
            'apikey'=>$this->key,
            'html'=>$html
        );
        $ch = curl_init($this->url);
        curl_setopt($ch,CURLOPT_POST,TRUE);
        curl_setopt($ch,CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
        
        $this->output = curl_exec($ch);
        
        curl_close($ch);
    }
}

class Engine{
    
     var $html;
     
     var $csscalled = NULL;
     
     var $jscalled = NULL;
     
    function __construct($title,$lang=NULL,$class=NULL){
        if ($class) {
            if (!is_array($class)) {
                $class = array($class);
            }
            $class = trim(implode(" ", $class));
        }
        if ($lang) {
            $lang = 'lang="'.$lang.'"';
        }
        $this->html = '<!DOCTYPE html>
<html'.$class.$lang.'>
<head>
    <title>'.$title.'</title>
    <!-- Set Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="core/favicon180x180.png">
    <link rel="icon" sizes="32x32" href="core/favicon32x32.png">
    <link rel="icon" sizes="16x16" href="core/favicon16x16.png">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">';
    }
    
    final function AddCSS($directory=NULL){
        if (!$this->csscalled) {
            $this->csscalled= TRUE;
            if (!$directory) {
                $directory = 'core'.DIRECTORY_SEPARATOR.'css';
            }
            if (is_dir($directory)) {
                $cssfiles = scandir($directory);
                $i=0;
                while ($i<count($cssfiles)){
                    if (strpos($cssfiles[$i], '.css')) {
                        $found[] = $cssfiles[$i];
                    }
                    $i++;
                }
                if (isset($found)) {
                    $this->html.='
    <!-- CSS load -->';
                    foreach ($found AS $css){
                        $this->html.='
<link rel="stylesheet" type="text/css" href="'.$directory.$css.'">';
                    }
                }
                $this->html.='
</head>
<body>';
            }
        }
        

    }

    final function AddJS($directory=NULL){
        if (!$this->csscalled) {
            $this->AddCSS();
        }
        if (!$this->jscalled) {
            $this->jscalled = TRUE;
            if (!$directory && !$this->jscalled) {
                $directory = 'core'.DIRECTORY_SEPARATOR.'js';
            }
            if (is_dir($directory && !$this->jscalled)) {
                $jsfiles = scandir($directory);
                $i=0;
                while ($i<count($jsfiles)){
                    if (strpos($jsfiles[$i], '.js')) {
                        $found[] = $jsfiles[$i];
                    }
                    $i++;
                }
            }

            if (isset($found)) {
                $this->html.='
    <!-- JS load -->';
                foreach ($found AS $css){
                    $this->html.='
<script type="text/javascript" src="'.$directory.$css.'"></script>';
                }
            }
        }

    }
    
}

class Text extends Engine{
    
    var $html;
    
    function __construct($html){
        if (!$html->html) {
            $engine = new engine('Missing engine start');
            $engine->AddCSS();
            $engine->AddJS();
            $this->html = $engine->html;
        }else{
            if (!$this->csscalled) {
                $this->AddCSS();
            }
            if (!$this->jscalled) {
                $this->AddJS();
            }
            $this->html = $html->html;
        }
        
    }
    
    function headings($string,$size) {
        if ($size<1||$size>6) {
            echo 'size error on function headings'.__LINE__;
        }
        $this->html.='
    <h'.$size.'>'.$string.'</h'.$size.'>';
    }
    
    function paragraph($string){
        $this->html.='
    <p>'.$string.'<p>';
    }
    
    function lineBreak(){
        $this->html.='
    <br>';
    }
    
    function thematicBreak($string){
        $this->html.='
<hr>'.$string.'</hr>';
    }
    
    function commentHTML($string){
        $this->html.='
<!--'.$string.'-->';
    }
    
    function template($buttonText,$content){
        $this->html.='<button onclick="showContent()">'.$buttonText.'</button>
<template>'.$content.'</template>
    
<script>
function showContent(){
    var temp = document.getElementsByTagName("template")[0];
    var clon = temp.content.cloneNode(true);
    document.body.appendChild(clon);
}
</script>';
        
    }
    
    function meter($label,$id,$value,$min=NULL,$max=NULL){
        if ($min&$max) {
            $min = ' min="'.$min.'" ';
            $max = 'max="'.$max.'"';
        }else{
            $min = NULL;
            $max = '"';
        }
        $this->html.='
<label for="'.$id.'">'.$label.'</label>
<meter id="'.$id.'" value="'.$value.'"'.$min.$max.'>'.$value.'</meter>';
    }
    
    function progress($label,$id,$value,$max){
        if ($value&$max) {
            $value = ' value="'.$value.'" ';
            $max = ' max="'.$max.'"';
        }
        $this->html.='
<label for="'.$id.'">'.$label.'</label>
<progress id="'.$id.'"'.$value.$max.'>'.$value.'</progress>';
    } 
    
    function wbr($string){
        $this->html.='<wbr>'.$string.'</wbr>';
    }
    
    function render($output=FALSE){
        $this->html.='
</body>
</html>';
        if ($output!=FALSE) {
            echo $this->html;
        }else{
            return $this->html;
        }
    }
    
    
}

class Format extends Engine{
    
    function __construct($html){
        if (!$html->html) {
            $engine = new engine('Missing engine start');
            $engine->AddCSS();
            $engine->AddJS();
            $this->html = $engine->html;
        }else{
            if (!$this->csscalled) {
                $this->AddCSS();
            }
            if (!$this->jscalled) {
                $this->AddJS();
            }
            $this->html = $html->html;
        }
        
    }
    
    function abbreviation($abbreviation,$string){
        $this->html.='
<abbr title="'.$abbreviation.'">'.$string.'</abbr>';
    
    }
    
    function address($string){
        $this->html.='<address>'.$string.'</address>';
    }
    
    function bold($string){
        $this->html.='<b>'.$string.'</b>';
    }
    
    function bidirectional($string){
        $this->html.='<bdo>'.$string.'</bdo>';
    }
    
    function blockquote($string){
        $this->html.='<blockquote>'.$string.'</blockquote>';
    }
    
    function cite($string){
        $this->html.='<cite>'.$string.'</cite>';
    }
    
    function del($string){
        $this->html.='<del>'.$string.'</del>';
    }
    
    function dfn($string){
        $this->html.='<dfn>'.$string.'</dfn>';
    }
    
    function em($string){
        $this->html.='<em>'.$string.'</em>';
    }
    
    function italic($string) {
        $this->html.='<i>'.$string.'</i>';
    }
    
    function ins($string){
        $this->html.='<ins>'.$string.'</ins>';
    }

    function mark($string){
        $this->html.='<mark>'.$string.'</mark>';
    }

    function quote($string){
        $this->html.='<q>'.$string.'</q>';
    }
    
    function small($string){
        $this->html.='<small>'.$string.'</small>';
    }
    
    function strong($string) {
        $this->html.='<strong>'.$string.'</strong>';
    }
    
    function sub($string){
        $this->html.='<sub>'.$string.'</sub>';
    }
    
    function sup($string){
        $this->html.='<sup>'.$string.'</sup>';
    }
    
    function underline($string){
        $this->html.='<u>'.$string.'</u>';
    }
 
    function render($output=FALSE){
        $this->html.='
</body>
</html>';
        if ($output) {
            echo $this->html;
        }else{
            return $this->html;
        }
    }
    
}

class FormatCode extends Format{
    
    function code($string){
        $this->html.='<code>'.$string.'</code>';
    }
    
    function samp($string){
        $this->html.='<samp>'.$string.'</samp>';
    }
    
    function kbd($string){
        $this->html.='<kdb>'.$string.'</kbd>';
    }
    
    function time($string){
        $this->html.='<time>'.$string.'</time>';
    }
    
    function pre($string){
        $this->html.='<pre>'.$string.'</pre>';
    }
    
    function variable($string){
        $this->html.='<var>'.$string.'</var>';
    }
    
}

class Meta extends Engine{
    
    function base($href,$target = NULL){
        if (!$target) {
            $target = '_blank';
        }
        $this->html.='<base href="'.$href.'" target="'.$target.'">';
    }

}



    