<style type="text/css">

* {
  -webkit-box-sizing: border-box;
          box-sizing: border-box;
}


/* ---- grid ---- */

.grid {
  //background: #EEE;
  //max-width: 1200px;
}

/* clearfix */
.grid:after {
  content: '';
  display: block;
  clear: both;
}

/* ---- grid-item ---- */

.grid-item {
    width: 360px;
    float: left;
    border-radius: 5px;
    //padding: 10px;
}

</style>



<div class="grid">
    <div class="grid-sizer"></div>
    <?php
    include_once('status/sensor_status.php');
    include_once('status/minmax_status.php');
    include_once('status/hosts_status.php');
    include_once('status/gpio_status.php');
    include_once('status/counters_status.php');
    include_once('status/relays_status.php');
    include_once('status/meteo_status.php');
    foreach (range(1, 10) as $v) {
	$ow=$v;
	include('status/ownwidget.php');
    }
    include('status/ipcam_status.php');
    include_once('status/ups_status.php');
    ?>
</div>

<script type="text/javascript">
    setInterval( function() {
    $(".ss").load("status/sensor_status.php");
    $('.co').load("status/counters_status.php");
    $('.gs').load("status/gpio_status.php");
    $('.hs').load("status/hosts_status.php");
    $('.rs').load("status/relays_status.php");
    $('.ms').load("status/meteo_status.php");
    $('.ow2').load("status/ownwidget2.php");
    $('.ow3').load("status/ownwidget3.php");
    $('.mm').load("status/minmax_status.php");
    $('.ups').load("status/ups_status.php");
    
}, 60000);

$(document).ready( function() {

  $('.grid').masonry({
    itemSelector: '.grid-item',
    columnWidth: 380
  });
  
});
</script>
<script src="html/masonry/masonry.pkgd.min.js"></script>





