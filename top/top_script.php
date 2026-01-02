<?php
if (isset($_REQUEST['edit']) && $_REQUEST['edit'] == 'yes') {
    $_SESSION['edita'] = 1;
}
if (isset($_REQUEST['edit']) && $_REQUEST['edit'] == 'no') {
    //setcookie('edit', $cookie_value, time() - (86400 * 30));
    unset($_SESSION['edita']);
    $_SESSION['edita'] = "";
}

include_once($tconfig["tpanel_path"].$templatePath."top/top_script.php");
include_once($tconfig["tpanel_path"]."top/validation.php");
include_once($tconfig["tpanel_path"]."script_validation_message_variables.php");

$DEFAULT_COUNTRY_CENTER_LATITUDE =$DEFAULT_COUNTRY_CENTER_LONGITUDE= "";

if(!empty($country_data_arr[$DEFAULT_COUNTRY_CODE_WEB]) && !empty($country_data_arr[$DEFAULT_COUNTRY_CODE_WEB]['tLatitude']) && !empty($country_data_arr[$DEFAULT_COUNTRY_CODE_WEB]['tLongitude'])){
    $DEFAULT_COUNTRY_CENTER_LATITUDE = $country_data_arr[$DEFAULT_COUNTRY_CODE_WEB]['tLatitude'];
    $DEFAULT_COUNTRY_CENTER_LONGITUDE = $country_data_arr[$DEFAULT_COUNTRY_CODE_WEB]['tLongitude'];
}
$SYSTEM_MAP_STYLE = SYSTEM_MAP_STYLE;
?>
<link href="<?= $siteUrl;?>assets/css/autocomplete_box.css" rel="stylesheet" type="text/css" />
<link href="<?= $siteUrl;?>assets/css/blur.css" rel="stylesheet" type="text/css" />
<link href="<?= $siteUrl;?>assets/css/common.css" rel="stylesheet" type="text/css" />
<script>
        var GOOGLE_SEVER_API_KEY_WEB = "<?=$GOOGLE_SEVER_API_KEY_WEB?>";
        var GOOGLE_SEVER_GCM_API_KEY = "<?=$GOOGLE_SEVER_GCM_API_KEY?>";
        var DEFAULT_COUNTRY_CENTER_LATITUDE = "<?=$DEFAULT_COUNTRY_CENTER_LATITUDE?>";
        var DEFAULT_COUNTRY_CENTER_LONGITUDE = "<?=$DEFAULT_COUNTRY_CENTER_LONGITUDE?>";
        var sess_lang = "<?=$_SESSION['sess_lang'];?>";
        var strategy = "<?=$MAPS_API_REPLACEMENT_STRATEGY;?>";
        var MAPS_API_REPLACEMENT_STRATEGY = "<?=$MAPS_API_REPLACEMENT_STRATEGY;?>";
        var GOOGLE_API_REPLACEMENT_URL = "<?=GOOGLE_API_REPLACEMENT_URL;?>";
        var TSITE_DB = "<?=TSITE_DB;?>";
		var tsite_url_base = "<?= $siteUrl;?>";
		var WEBSERVICE_API_FILE_NAME = tsite_url_base+"<?=WEBSERVICE_API_FILE_NAME;?>";
		var SITE_PANEL_PATH = '<?= $tconfig["tsite_folder"] ?>';
		var SYSTEM_MAP_STYLE = <?= $SYSTEM_MAP_STYLE ?>;
		var SIGN_IN_OPTION = '<?= $SIGN_IN_OPTION ?>';

		if(MAPS_API_REPLACEMENT_STRATEGY.toUpperCase() != "ADVANCE"){
			MAPS_API_REPLACEMENT_STRATEGY = "None";
		}

		function isRetinaDisplay() {
            if (window.matchMedia) {
                var mq = window.matchMedia("only screen and (min--moz-device-pixel-ratio: 1.3), only screen and (-o-min-device-pixel-ratio: 2.6/2), only screen and (-webkit-min-device-pixel-ratio: 1.3), only screen  and (min-device-pixel-ratio: 1.3), only screen and (min-resolution: 1.3dppx)");
                return (mq && mq.matches || (window.devicePixelRatio > 1));
            }
		}

		var isRatinaDisplay = isRetinaDisplay();
		var TSITE_SC_PROTOCOL = "<?php echo($tconfig['tsite_sc_protocol']); ?>";
		var TSITE_SC_HOST = "<?php echo($tconfig['tsite_sc_host']); ?>";
		var TSITE_HOST_SC_PATH = "<?php echo($tconfig['tsite_host_sc_path']); ?>";
		var TSITE_HOST_SC_PORT = "<?php echo($tconfig['tsite_host_sc_port']); ?>";
</script>

<script type='text/javascript' src='<?= $siteUrl;?>assets/js/ajax_for_advance_strategy.js'></script>
<script type='text/javascript' src='<?= $siteUrl;?>assets/js/network_js.js'></script>
<script type='text/javascript' src='<?= $siteUrl;?>assets/js/reverse_geo_code.js'></script>
<script type='text/javascript' src='<?= $siteUrl;?>assets/js/reverse_geo_direction_code.js'></script>
<script type="text/javascript" src="<?= $siteUrl;?>assets/js/getDataFromApi.js"></script>
<script type="text/javascript" src="<?= $siteUrl;?>assets/js/convertToAllLanguage.js"></script>
<script type="text/javascript" src="<?= $siteUrl;?>assets/libraries/scClient-js/socketcluster-client.js"></script>
<script type="text/javascript" src="<?= $siteUrl;?>assets/js/socketclustercls.js"></script>
<script type="text/javascript" src="<?= $siteUrl;?>assets/js/common.js"></script>
<script type="text/javascript" src="<?= $siteUrl;?>assets/js/resizeimageplace.js"></script>

<style>
/* Progress bar start */
	.progress-indeterminate{
			height: 2px;
			position: relative;
			bottom: 0;
			padding: 0 ;
			margin-bottom: 0px !important;
			overflow:hidden;
			box-shadow:none !important;
			background-color: transparent;
	}
	.progress-bar{
		background-color:#219201;
	}
	.progress-bar.indeterminate {
		box-shadow:none !important;
		position: relative;
	  /* animation: progress-indeterminate 2s linear infinite; */
	  animation : progress-indeterminate 2s cubic-bezier(.6,.04,.98,.34) infinite;
	    background-image:
		-webkit-linear-gradient(-60deg, transparent 33%, rgba(0, 0, 0, .1) 33%, rgba(0, 0, 0, 0) 33%, transparent 33%), -webkit-linear-gradient(top, rgba(255, 255, 255, .10), rgba(241, 234, 234, 0.88)), -webkit-linear-gradient(left, #ffff,#219201);
		border-radius: 50px;
		background-size: 35px 20px, 100% 100%, 100% 100%;
	}
	@keyframes progress-indeterminate {
	   from { left: -85%; width: 85%; }
	   to { left: 100%; width: 85%;}
	}
	.box_in_map{position:relative;} /* box_in_map use for textbox in google map  */
	.box_in_map .progress-indeterminate { position:absolute; bottom:0px;height: 2px;width: 21.50% !important;top: 27px;right: 0px;}
	.form-column-full .progress-indeterminate{width:100% !important;}
	.half.newrow .progress-indeterminate{width:100% !important;}
	.map_to_createrequest .progress-indeterminate{ position:absolute; top:45px !important;height: 2px;width: 44.50% !important;right:25px !important;}
	.drop-location .progress-indeterminate{height: 2px;width: 97% !important;z-index: 1;margin-left: 10px;bottom: 0px;right: auto;}
	.ui-autocomplete .ui-menu-item{
		list-style-image:none !important;
	}

 /* Progress bar end */
</style>
<h1 style="height: 0;margin: 0;padding: 0;pointer-events: none;visibility: hidden; font-size: 0;">8CAnbE</h1>
<script>
	if(GetGeoCookie("isRatinaDisplay") == "") {
		document.cookie = "isRatinaDisplay=" + isRatinaDisplay;
		location.reload();
	}
	var ua = navigator.userAgent.toLowerCase();
	var otherbrowser = 1;
		/*if (ua.indexOf('safari') != -1) {
		  if (ua.indexOf('chrome') > -1) {
		  	var otherbrowser = 1;
		    // alert("1") // Chrome
		  } else {
		     otherbrowser = 0;
		    // var timezone = Int.DateTimeFormat().resolvedOptions().timeZone;
    		// document.cookie = "vUserDeviceTimeZone=" + timezone;
		  }
		}*/
	if(otherbrowser == 1){
		var timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
		document.cookie = "vUserDeviceTimeZone=" + timezone;
	}

	function getReviseImageHeight(height) {
		if(isRatinaDisplay) {
			height = height * 2;
		}

		return height;
	}
</script>
<?=$GOOGLE_ANALYTICS;?>
<?php
$isRatinaDisplay = $_COOKIE['isRatinaDisplay'];

//added by SP on 29-06-2019 for disallow other css and apply css which are given by ckeditor
$filename = basename($_SERVER['REQUEST_URI'], '?' . $_SERVER['QUERY_STRING']);
if ($filename == 'Page-Not-Found' || $filename == 'about' || $filename == 'help-center' || $filename == 'terms-condition' || $filename == 'how-it-works' || $filename == 'trust-safety-insurance' || $filename == 'privacy-policy' || $filename == 'legal') {
    ?>

    <script>
        $(document).ready(function () {
            $(".static-page ol li").each(function (index) {
                $(this).attr('data-number', index + 1);
            });
        })
    </script>

    <style>
        strong {
            font-weight : bold;
        }
        em {
            font-style : italic;
        }
        u {
            text-decoration: underline;
        }
        s {
            text-decoration: line-through;
        }
        .static-page ol li:before {
            content: attr(data-number);
            position: absolute;
            left: 0;
            font-size: 14px;
            font-weight: 600;
            padding-top: 6px;
        }
        .static-page ol li {
            background:none;
            position:relative;
        }
        .gen-cms-page ol li p,.gen-cms-page ol li{
        	padding-left: 15px
        }
    </style>
<?php }?>