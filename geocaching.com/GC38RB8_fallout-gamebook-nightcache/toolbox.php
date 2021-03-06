<?php

require_once('custom.php');
require_once('token.php');
require_once('ipcheck.php');
require_once('stats.php');

/**
 * overi poradi klicu.
 *
 */
function PORADI()
{
    $numargs = func_num_args();
    if ($numargs < 3) {
	echo "spatne parametry PORADI!";
	return false;
    }

    $poradi = func_get_args();
    $vstup = array_shift($poradi);
 
    $lastpos = -1;
    foreach ($vstup as $v) {
	$pos = array_search($v, $poradi);
	// pokud je klic v zadanem poradi over jeho index
	if ($pos !== false) {
		if ($pos <= $lastpos) {
		    return false;
		}
		$lastpos = $pos;
	}
     }
     return true;
}

function PRIDEJ_PERK(&$U)
{
    $arg_list = func_get_args();
    array_shift($arg_list);
    foreach ($arg_list as $perk) {
	if (!in_array($perk, $U['perky'])) {
	    array_push($U['perky'], $perk);
	}
    }
}

function ODEBER_PERK(&$U)
{
    $arg_list = func_get_args();
    array_shift($arg_list);
    foreach ($arg_list as $perk) {
	$key = array_search($perk, $U['perky']);
	if ($key !== FALSE) {
	    unset($U['perky'][$key]);
	}
    }
}

function MA_KLICE()
{
    $arg_list = func_get_args();
    $U = array_shift($arg_list);
    foreach ($arg_list as $klic) {
	if (!in_array($klic, $U['keylist'])) {
	    return false;
	}
    }
    return true;
}

function parse_keys($klice_str)
{
  $keys = array();
  $keys_nums = preg_match_all('/\b\d+\b/', $klice_str, $keys);
  $klice = array();
  
  foreach ($keys[0] as $k) {
//    echo "<br />klic" . intval($k); // DEBUG
    $klice[] = intval($k);
  }
  return $klice;
}

function ohodnot_hrace(&$U)
{
  $U['keylist'] = parse_keys($U['klice']);

  $U['perky'] = array();
  vyhodnot_klice($U);

  if(!over_platnost($U)) {
   /// cheater
   dbstats_update($U, '', 'cheater');
   echo "<p>Cheater !!!</p>\n";
   if (KICK_CHEATERS) {
	die();
   }
  }
  spocti_skore($U);
}

/**
 * Vrati pole obsahujici klice vstupniho pole, ktere maji hodnotu true.
 *
 * Example:
 * $v = array( 1=>true, 2=>false, 13=>true);
 * $res = array_keys_true($v);
 * // $res == array(1, 13);
 */
function array_keys_true($keys)
{
  $res = array();
  foreach ($keys as $k => $v) {
    if ($v) {
	$res[] = $k;
    }
  }
  return $res;
}
