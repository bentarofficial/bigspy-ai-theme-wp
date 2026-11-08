<?php
define('ABSPATH','/');
class WP_Error { public $m; function __construct($c,$m){$this->m=$m;} function get_error_message(){return $this->m;} }
function is_wp_error($x){return $x instanceof WP_Error;}
function __($s){return $s;} function sanitize_key($k){return preg_replace('/[^a-z0-9_\-]/','',strtolower($k));}
function wp_json_encode($x){return json_encode($x);}
require $argv[1];
$out=[];
foreach(json_decode(file_get_contents($argv[2]),true) as $c){ foreach($c['runs'] as $r){ $res=bst_calc_run($c['fields'],$r); ksort($res); $o=[]; foreach($res as $k=>$v){$o[$k]=$v['status'].':'.$v['text'];} $out[]=$c['label'].' '.json_encode($r,JSON_UNESCAPED_UNICODE).' => '.json_encode($o,JSON_UNESCAPED_UNICODE);} }
echo implode("\n",$out),"\n";
