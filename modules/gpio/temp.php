<?php
//gpio, czas, rownasie, metoda
// week list
// hit po week w temp
// on off
// exit

$db = new PDO('sqlite:../../dbf/nettemp.db');
$debug = isset($_GET['debug']) ? $_GET['debug'] : '';


////////////////////////////////////////////////////////
// functions


function timestamp($gpio,$onoff) {
	
	if (file_exists("../../db/gpio_stats_$gpio.sql")) {
		$db = new PDO("sqlite:../../db/gpio_stats_$gpio.sql") or die ("ts 1\n" );
	   $db->exec("INSERT OR IGNORE INTO def (value) VALUES ('$onoff')") or die ("ts 2\n" );
  	}
	else {
		$db = new PDO("sqlite:../../db/gpio_stats_$gpio.sql");
   	$db->exec("CREATE TABLE def (time DATE DEFAULT (datetime('now','localtime')), value INTEGER)") or die ("ts 3\n" );
    	$db->exec("INSERT OR IGNORE INTO def (value) VALUES ('$onoff')") or die ("ts 4\n" );
	}
}

function w_profile_check($gpio,$w_profile) {
	$day=date("D");
	$time=date("Hi");
	$db = new PDO('sqlite:../../dbf/nettemp.db');
	$rows = $db->query("SELECT * FROM day_plan WHERE name='$w_profile' AND (Mon='$day' OR Tue='$day' OR Wed='$day' OR Thu='$day' OR Fri='$day' OR Sat='$day' OR Sun='$day')");
	$row = $rows->fetchAll();
	$numRows = count($row);
 	if ( $numRows > '0' ) { 
		foreach ($row as $b) {
			$stime=$b['stime'];
			$etime=$b['etime'];
		}
		if($time >= $stime && $time < $etime) {
				 return true;
		}
			else {
				return false;
			}
	}
		else {
			return false;
		}
}

function action_on($op,$sensor_name,$gpio,$rev) {
	$db = new PDO('sqlite:../../dbf/nettemp.db');
	$out="/usr/local/bin/gpio -g mode $gpio output";
	$read="/usr/local/bin/gpio -g read $gpio";
	$on="/usr/local/bin/gpio -g write $gpio 1";
	$off="/usr/local/bin/gpio -g write $gpio 0";
	system($out);
	system($read, $check);
	if ($rev == 'on') {
	 if ($check == "1"){ 
	    system($off);
	 }
		else {
	 		if ($check == '0'){ 
	    	system($on);
	 		}
		}
	}	
	$db->exec("UPDATE gpio SET status='ON',state='ON' WHERE gpio='$gpio'");
  	echo "GPIO ".$gpio." ".$rev." TRUN ON\n";
  	$onoff='1';
  	timestamp($gpio,$onoff);
}
function action_off($op,$sensor_name,$gpio,$rev) {
	$db = new PDO('sqlite:../../dbf/nettemp.db');
	$out="/usr/local/bin/gpio -g mode $gpio output";
	$read="/usr/local/bin/gpio -g read $gpio";
	$on="/usr/local/bin/gpio -g write $gpio 1";
	$off="/usr/local/bin/gpio -g write $gpio 0";
	system($out);
	system($read, $check);
	if ($rev == 'on') {
	 if ($check == "0"){ 
	    system($on);
	 }
		else {
	 		if ($check == '1'){ 
	    	system($off);
	 		}
		}
	}
	$db->exec("UPDATE gpio SET status='ON',state='ON' WHERE gpio='$gpio'");
	echo "GPIO ".$gpio." ".$rev." TRUN OFF\n";
	$onoff='0';
	timestamp($gpio,$onoff);

}


function hyst($op,$sensor_tmpadj,$value,$sensor_name,$op,$gpio,$rev,$hyst,$value_max,$state,$onoff) {
	
}


		
// main loop
$rows = $db->query("SELECT * FROM gpio WHERE mode='temp'");
$row = $rows->fetchAll();
foreach ($row as $a) {
	$gpio=$a['gpio'];
	$rev=$a['rev']; 
		if($rev=='on') {$mode='LOW';} else {$mode='HIGH'; $rev=null;}
	$day_run=$a['day_run'];
	$state=$a['state'];
	echo "GPIO ".$gpio.", Mode=".$mode.", ".date('l jS \of F Y H:i:s')."\n";
		
	$rows = $db->query("SELECT * FROM g_func WHERE gpio='$gpio' ORDER BY position ASC");
	$func = $rows->fetchAll();
	//get data function for gpio
	foreach ($func as $func) {
		   $f_id=$func['id'];
			$sens_id=$func['sensor'];
			$sens2_id=$func['sensor2'];
			$op=$func['op'];
			$onoff=$func['onoff'];
			$source=$func['source'];
			$hyst=$func['hyst'];
			$w_profile=$func['w_profile'];			
			
			//check if in week profile, if not exit, if empty or in range go forward
			if(($day_run=='on') && ($w_profile!='any')) {
				if (w_profile_check($gpio,$w_profile)===false){
					echo "Function ".$f_id." with profile '".$w_profile."' not in range.\n";
					continue;
				} 
				else {
					echo "Function ".$f_id." with profile '".$w_profile."' hit.\n";
				}
			}
			//hit in any
			elseif($w_profile!='any') {
					echo "Function ".$f_id." with profile '".$w_profile."' not in range. Day off.\n";
					continue;
				}
				else {
					echo "Function ".$f_id." with profile '".$w_profile."' hit.\n";
				}
			
						
			$rows = $db->query("SELECT * FROM sensors WHERE id='$sens_id'");
			$sens = $rows->fetchAll();
			foreach ($sens as $sens) {
				$sensor_name=$sens['name'];
				$sensor_tmp=$sens['tmp'];
				$sensor_tmpadj=$sens['tmp']+$sens['adj'];
					}
			// define what is value
			if($source == 'temp' || $source == 'temphyst') {
					$value=$func['value'];
					$value_max=$func['value']+$func['hyst'];
					$sensor2_name=null;
					$sensor2_tmp=null;
					$sensor2_tmpadj=null;
			}
			elseif($source == 'sensor2' || $source == 'sensor2hyst') {
					$rows2 = $db->query("SELECT * FROM sensors WHERE id='$sens2_id'");
					$sens2 = $rows2->fetchAll();
					foreach ($sens2 as $sens2) {
						$sensor2_name=$sens2['name'];
						$sensor2_tmp=$sens2['tmp'];
						$sensor2_tmpadj=$sens2['tmp']+$sens2['adj'];
					}
					$value=$sensor2_tmpadj;
					$value_max=$sensor2_tmpadj+$func['hyst'];
			}

			if($debug=='yes') {
				echo $gpio." ".$sensor_name." ".$sensor_tmpadj." ".$op." ".$sensor2_name." ".$value." ".$hyst." ".$value_max." ".$onoff." ".$w_profile."\n";
			}
			//////// temp function
			if($source=='temp' || $source=='sensor2') {
				$func = 'action_' . $onoff;
				if ($op=='gt') {
					if ($sensor_tmpadj > $value){
						print $func($op,$sensor_name,$gpio,$rev);
						break;
					}
				} 
				elseif ($op=='ge') {
					if ($sensor_tmpadj >= $value){
						print $func($op,$sensor_name,$gpio,$rev);	
						break;
					}
				}		 
				elseif ($op=='le') {
					if ($sensor_tmpadj <= $value){
						print $func($op,$sensor_name,$gpio,$rev);
						break;
					}
				} 
				elseif ($op=='lt') {
					if ($sensor_tmpadj < $value){
						print $func($op,$sensor_name,$gpio,$rev);	
						break;
					}
				}
			} 
			////////// hyst function
			elseif($source=='temphyst' || $source=='sensor2hyst') {
				if ($op=='gt') {
					if ($sensor_tmpadj > $value){
						echo "gt 1 on\n";
						action_on($op,$sensor_name,$gpio,$rev);
						break;
					}
					elseif($sensor_tmpadj < $value && $state == 'on' ) {
						echo "gt 2 on running\n";
						action_on($op,$sensor_name,$gpio,$rev);
						break;
					}
					elseif($sensor_tmpadj < $value && $sensor_tmpadj < $value_max) {
						echo "gt 3 off\n";
						action_off($op,$sensor_name,$gpio,$rev);	
						break;
					}
					elseif($sensor_tmpadj < $value && $state == 'off') {
						echo "gt 4 off going down\n";
						action_off($op,$sensor_name,$gpio,$rev);	
						break;
					}
				} 
				elseif ($op=='ge') {
				if ($sensor_tmpadj >= $value){
						echo "ge 1 on\n";
						echo "state on\n";
						action_on($op,$sensor_name,$gpio,$rev);
						break;
					}
					elseif($sensor_tmpadj <= $value && $state == 'on' ) {
						echo "ge 2 on running\n";
						echo "state on\n";	
						action_on($op,$sensor_name,$gpio,$rev);	
						break;
					}
					elseif($sensor_tmpadj <= $value && $sensor_tmpadj < $value_max) {
						echo "ge 3 off\n";
						action_off($op,$sensor_name,$gpio,$rev);
						break;
					}
					elseif($sensor_tmpadj <= $value && $state == 'off') {
						echo "ge 4 off going down\n";
						action_off($op,$sensor_name,$gpio,$rev);	
						break;
					}
				} 
				elseif ($op=='le') {
					if ($sensor_tmpadj <= $value){
						echo "le 1 on\n";
						action_on($op,$sensor_name,$gpio,$rev);
						break;
					}
					elseif($sensor_tmpadj >= $value && $state == 'on' ) {
						echo "le 2 on running\n";
						action_on($op,$sensor_name,$gpio,$rev);	
						break;
					}
					elseif($sensor_tmpadj >= $value && $sensor_tmpadj > $value_max) {
						echo "le 3 off\n";
						action_off($op,$sensor_name,$gpio,$rev);	
						break;
					}
					elseif($sensor_tmpadj >= $value && $state == 'off') {
						echo "le 4 off going down\n";
						action_off($op,$sensor_name,$gpio,$rev);	
						break;
					}
			} 
				elseif ($op=='lt') {
					if ($sensor_tmpadj < $value){
						echo "lt 1 on\n";
						action_on($op,$sensor_name,$gpio,$rev);
						break;
					}
					elseif($sensor_tmpadj > $value && $state == 'on' ) {
						echo "lt 2 on running\n";
						action_on($op,$sensor_name,$gpio,$rev);	
						break;
					}
					elseif($sensor_tmpadj > $value && $sensor_tmpadj > $value_max) {
						echo "lt 3 off\n";
						action_off($op,$sensor_name,$gpio,$rev);	
						break;
					}
					elseif($sensor_tmpadj > $value && $state == 'off') {
						echo "lt 4 off going down\n";
						action_off($op,$sensor_name,$gpio,$rev);	
						break;
					}
				}
			}

	
	} //function
	unset($rev);
}  //main
?>