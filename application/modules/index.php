<?php
//return "<h1>It works!</h1>";
return Request::getUri()."<pre>".print_r($_SERVER,true)."<pre>";