<script type="text/JavaScript">
	    function showtemp<?php echo $gpio ?>(n) {
		if (document.getElementById('state<?php echo $gpio ?>' + n).value == 'temp') {
		    document.getElementById('inputtemp<?php echo $gpio ?>' + n).style.display = 'block';
		    
		    document.getElementById('sensor2<?php echo $gpio ?>' + n).style.display = 'none';
		    document.getElementById('inputhyst<?php echo $gpio ?>' + n).style.display = 'none';
	
		    document.getElementById('inputtemp<?php echo $gpio ?>' + n).required = true;
		    document.getElementById('inputhyst<?php echo $gpio ?>' + n).required = false;
		}
		if (document.getElementById('state<?php echo $gpio ?>' + n).value == 'sensor2') {
		    document.getElementById('inputtemp<?php echo $gpio ?>' + n).style.display = 'none';
		    document.getElementById('sensor2<?php echo $gpio ?>' + n).style.display = 'block';
		    document.getElementById('inputhyst<?php echo $gpio ?>' + n).style.display = 'none';

		    document.getElementById('inputtemp<?php echo $gpio ?>' + n).required = false;
		    document.getElementById('inputhyst<?php echo $gpio ?>' + n).required = false;
		}
		if (document.getElementById('state<?php echo $gpio ?>' + n).value == 'temphyst') {
		    document.getElementById('inputtemp<?php echo $gpio ?>' + n).style.display = 'block';
		    document.getElementById('inputtemp<?php echo $gpio ?>' + n).required = true;
		    document.getElementById('sensor2<?php echo $gpio ?>' + n).style.display = 'none';
		    document.getElementById('inputhyst<?php echo $gpio ?>' + n).style.display = 'block';
		    document.getElementById('inputhyst<?php echo $gpio ?>' + n).required = true;
		}
		if (document.getElementById('state<?php echo $gpio ?>' + n).value == 'sensor2hyst') {
		    document.getElementById('inputtemp<?php echo $gpio ?>' + n).style.display = 'none';
		    document.getElementById('sensor2<?php echo $gpio ?>' + n).style.display = 'block';
		    document.getElementById('inputhyst<?php echo $gpio ?>' + n).style.display = 'block';
		    document.getElementById('inputhyst<?php echo $gpio ?>' + n).required = true;
		}
	    }
	    </script>
<?php

$fnum=$a['fnum'];

$fa='fa'.$gpio;
$fd='fd'.$gpio;
$$fa = isset($_POST["fa".$gpio]) ? $_POST["fa".$gpio] : '';
$$fd = isset($_POST["fd".$gpio]) ? $_POST["fd".$gpio] : '';

if ($$fa == "fa") {
    $asum=($fnum + 1);
    $db->exec("UPDATE gpio SET fnum='$asum' WHERE gpio='$gpio_post'") or die("exec fa");
    header("location: " . $_SERVER['REQUEST_URI']);
    exit();
}

if ($$fd == "fd") {
    if ($fnum == '1') {
	header("location: " . $_SERVER['REQUEST_URI']);
	exit();
    }
    $dsum=($fnum - 1);
    $db->exec("UPDATE gpio SET fnum='$dsum' WHERE gpio='$gpio_post'") or die("exec fd");
    header("location: " . $_SERVER['REQUEST_URI']);
    exit();
}


// zmienne isset
foreach (range(1, $fnum) as $ta) {
$temp_temp='temp_temp'.$ta;
$temp_sensor='temp_sensor'.$ta;
$temp_sensor_diff='temp_sensor_diff'.$ta;
$temp_onoff='temp_onoff'.$ta;
$temp_op='temp_op'.$ta;
$temp_hyst='temp_hyst'.$ta;
$temp_source='temp_source'.$ta;
$temp_set='temp_set'.$ta;
$temp_week_plan='temp_week_plan'.$ta;

$$temp_sensor=isset($_POST[$temp_sensor]) ? $_POST[$temp_sensor] : '';
$$temp_sensor_diff=isset($_POST[$temp_sensor_diff]) ? $_POST[$temp_sensor_diff] : '';
$$temp_onoff=isset($_POST[$temp_onoff]) ? $_POST[$temp_onoff] : '';
$$temp_op=isset($_POST[$temp_op]) ? $_POST[$temp_op] : '';
$$temp_temp=isset($_POST[$temp_temp]) ? $_POST[$temp_temp] : '';
$$temp_hyst=isset($_POST[$temp_hyst]) ? $_POST[$temp_hyst] : '';
$$temp_source=isset($_POST[$temp_source]) ? $_POST[$temp_source] : '';
$$temp_set=isset($_POST[$temp_set]) ? $_POST[$temp_set] : '';
$$temp_week_plan=isset($_POST[$temp_week_plan]) ? $_POST[$temp_week_plan] : '';


//var_dump($_POST);
//print_r($$temp_set.$ta);
//echo $ta;
//echo $fnum;


if ($$temp_set == "on") {

    if ($$temp_source == 'temp') {
	    $temp_temp=$$temp_temp;
	    $temp_sensor_diff=NULL;
	    $temp_hyst=NULL;
	    $temp_onoff=$$temp_onoff;
    } elseif ($$temp_source == 'sensor2') {
	    $temp_temp=NULL;
	    $temp_sensor_diff=$$temp_sensor_diff;
	    $temp_hyst=NULL;
	    $temp_onoff=$$temp_onoff;
    } elseif ($$temp_source == 'temphyst') {
	    $temp_temp=$$temp_temp;
	    $temp_sensor_diff=NULL;
	    $temp_hyst=$$temp_hyst;
	    $temp_onoff='on';
    } elseif ($$temp_source == 'sensor2hyst') {
	    $temp_temp=NULL;
	    $temp_sensor_diff=$$temp_sensor_diff;
	    $temp_hyst=$$temp_hyst;
	    $temp_onoff='on';
    }
    
    $temp_sensor=$$temp_sensor;
    $temp_op=$$temp_op;
    $temp_source=$$temp_source;
    $temp_week_plan=$$temp_week_plan;
    
    $db->exec("UPDATE gpio SET temp_source$ta='$temp_source',temp_op$ta='$temp_op',temp_sensor$ta='$temp_sensor',temp_sensor_diff$ta='$temp_sensor_diff',temp_onoff$ta='$temp_onoff',temp_temp$ta='$temp_temp',temp_hyst$ta='$temp_hyst',temp_week_plan$ta='$temp_week_plan' WHERE gpio='$gpio_post'") or die("exec 1");
    $db = null;
    header("location: " . $_SERVER['REQUEST_URI']);
    exit();
}

}

?>


<div class="panel panel-default">
<div class="panel-heading">Temperature functions <?php echo $fnum ?></div>
<div class="table-responsive">
<table class="table">
<thead><tr><th>Sensor1</th><th>State</th><th>Source</th><th>Value</th><th>Hysteresis</th><th>On/Off</th><th>Week Profile</th></tr></thead>
<div class="form-group">
<?php
    foreach (range(1, $fnum) as $v) {
?>
<tr>
<form class="form-horizontal" action="" method="post">
<td class="col-md-1">
<select name="<?php echo temp_sensor . $v; ?>" class="form-control input-sm">
<?php $sth = $db->prepare("SELECT * FROM sensors");
    $sth->execute();
    $result = $sth->fetchAll();
    foreach ($result as $select) { ?>
	<option <?php echo $a['temp_sensor'.$v] == $select['id'] ? 'selected="selected"' : ''; ?> value="<?php echo $select['id']; ?>"><?php echo "{$select['name']} {$select['tmp']}" ?></option>
<?php 
    } 
?>
</select>
</td>

<td class="col-md-1">
<select name="<?php echo temp_op . $v ?>" class="form-control input-sm">
    <option <?php echo $a['temp_op'.$v] == 'lt' ? 'selected="selected"' : ''; ?> value="lt">&lt;</option>   
    <option <?php echo $a['temp_op'.$v] == 'le' ? 'selected="selected"' : ''; ?> value="le">&lt;&#61;</option>     
    <option <?php echo $a['temp_op'.$v] == 'gt' ? 'selected="selected"' : ''; ?> value="gt">&gt;</option>   
    <option <?php echo $a['temp_op'.$v] == 'ge' ? 'selected="selected"' : ''; ?> value="ge">&gt;&#61;</option>   
</select>
</td>

<td class="col-md-1">
    <select name="<?php echo temp_source . $v; ?>" class="form-control input-sm" id="<?php echo state.$gpio.$v; ?>" onclick='showtemp<?php echo $gpio ?>(<?php echo $v; ?>)'>
	<option <?php if ((!empty($a['temp_hyst'.$v])) && (!empty($a['temp_hyst'.$v]))) { echo 'selected="selected"';} ?> value="temphyst">Temp+Histeresis</option>
	<option <?php if ((!empty($a['temp_temp'.$v])) && (empty($a['temp_hyst'.$v]))) { echo 'selected="selected"';} ?> value="temp">Temp</option>
	<option <?php if ((!empty($a['temp_sensor_diff'.$v])) && (empty($a['temp_hyst'.$v]))) { echo 'selected="selected"';} ?> value="sensor2">Sensor2</option>
	<option <?php if ((!empty($a['temp_sensor_diff'.$v])) && (!empty($a['temp_hyst'.$v]))) { echo 'selected="selected"';} ?> value="sensor2hyst">Sensor2+Histeresis</option>
    </select>
</td>

<td class="col-md-1">
<?php 
	$sth = $db->prepare("SELECT * FROM sensors");
	    $sth->execute();
	    $result = $sth->fetchAll();
	    $sensor2c = count($result);
	?>
    
    <select id="<?php echo sensor2.$gpio.$v; ?>" name="<?php echo temp_sensor_diff.$v; ?>" class="form-control input-sm" <?php if (($a['temp_source'.$v] == 'sensor2') || ($a['temp_source'.$v] == 'sensor2hyst')) { echo 'style="display: block"'; } else { echo 'style="display: none"'; } ?> >
	<?php
	    foreach ($result as $select) { ?>
		<option <?php echo $a['temp_sensor_diff'.$v] == $select['id'] ? 'selected="selected"' : ''; ?> value="<?php echo $select['id']; ?>"><?php echo $select['name']." ".$select['tmp'] ?></option>
	    <?php } ?>
    </select>
    
    <input id="<?php echo inputtemp.$gpio.$v; ?>"  type="text" name="<?php echo temp_temp . $v ?>" value="<?php echo $a['temp_temp'.$v]; ?>" class="form-control input-sm" <?php if (($a['temp_source'.$v] == 'temp') || ($a['temp_source'.$v] == 'temphyst') || (empty($a['temp_source'.$v]))) { echo 'style="display: block" required=""'; } else { echo 'style="display: none"'; } ?> >
</td>

<td class="col-md-1">
    <input id="<?php echo inputhyst.$gpio.$v; ?>" type="text" name="<?php echo temp_hyst . $v ?>" value="<?php echo $a['temp_hyst'.$v]; ?>" class="form-control input-sm" <?php if (($a['temp_source'.$v] == 'temphyst') || ($a['temp_source'.$v] == 'sensor2hyst') || (empty($a['temp_source'.$v]))) { echo 'style="display: block" required=""'; } else { echo 'style="display: none"'; } ?> >
</td>

<td class="col-md-1">
<select name="<?php echo temp_onoff . $v ?>" class="form-control input-sm">
    <option <?php echo $a['temp_onoff'.$v] == 'on' ? 'selected="selected"' : ''; ?> value="on">ON</option>
    <option <?php echo $a['temp_onoff'.$v] == 'off' ? 'selected="selected"' : ''; ?> value="off">OFF</option>
    <option <?php echo $a['temp_onoff'.$v] == 'onoff' ? 'selected="selected"' : ''; ?> value="onoff">ON/OFF</option>
</select>
</td>

<td class="col-md-1">
<?php
	$sth = $db->prepare("SELECT name FROM day_plan WHERE gpio=$gpio");
	    $sth->execute();
	    $result = $sth->fetchAll();
	    $sensor2c = count($result);
	?>
    <select id="" name="<?php echo temp_week_plan.$v; ?>" class="form-control input-sm" <?php echo $a['day_run'] != 'on' ? 'disabled="disabled"' : ''; ?>>
	<?php 
	    foreach ($result as $wp) { ?>
		<option <?php echo $a['temp_week_plan'.$v] == $wp['name'] ? 'selected="selected"' : ''; ?> value="<?php echo $wp['name']; ?>"><?php echo $wp['name']?></option>
	<?php } ?>
    </select>
</td>

<td class="col-md-1">
	<input type="hidden" name="gpio" value="<?php echo $a['gpio']; ?>"/>
	<input type="hidden" name="<?php echo temp_set.$v ?>" value="on" />
	<button type="submit" class="btn btn-xs btn-success"><span class="glyphicon glyphicon-save"></span> Save</button>
</form>

<?php if ($v == '1' && $fnum <= '9') { ?>
    <form action="" method="post" style=" display:inline!important;">
        <input type="hidden" name="<?php echo fa.$a['gpio'] ?>" value="fa" />
	<input type="hidden" name="gpio" value="<?php echo $a['gpio']; ?>"/>
        <button class="btn btn-xs btn-success"><span class="glyphicon glyphicon-plus"></span></button>
    </form>
<?php 
    }
if ($v == $a['fnum'] && $v != '1') { ?>
    <form action="" method="post" style=" display:inline!important;">
        <input type="hidden" name="<?php echo fd.$a['gpio'] ?>" value="fd" />
	<input type="hidden" name="gpio" value="<?php echo $a['gpio']; ?>"/>
        <button class="btn btn-xs btn-danger"><span class="glyphicon glyphicon-trash"></span></button>
    </form>
<?php
    }
?>
</td>

</tr>


<?php
    }
?>
</div>
</table>
</div>


</table>
</div>

