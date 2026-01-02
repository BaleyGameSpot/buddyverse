<?php if((isset($_REQUEST['ENABLE_COLOR_PICKER']) && strtoupper($_REQUEST['ENABLE_COLOR_PICKER']) == "YES") || isset($_COOKIE['ENABLE_COLOR_PICKER'])) { ?>
<script src="<?= $siteUrl ?>assets/plugins/colorpicker/js/jquery.minicolors.min.js"></script>
<script src="<?= $siteUrl ?>assets/plugins/colorpicker/js/color.js"></script>
<link rel="stylesheet" href="<?= $siteUrl ?>assets/plugins/colorpicker/css/jquery.minicolors.css">
<script type="text/javascript">
	$(document).ready( function() {
		$('.system-color-picker').trigger('change');
		$('.system-color-picker').minicolors({
			control: $(this).attr('data-control') || 'hue',
			defaultValue: $(this).attr('data-defaultValue') || '#000000',
			format: $(this).attr('data-format') || 'hex',
			keywords: $(this).attr('data-keywords') || '',
			inline: $(this).attr('data-inline') === 'true',
			letterCase: $(this).attr('data-letterCase') || 'lowercase',
			opacity: $(this).attr('data-opacity'),
			position: $(this).attr('data-position') || 'bottom',
			swatches: $(this).attr('data-swatches') ? $(this).attr('data-swatches').split('|') : [],
			change: function(hex, opacity) {
				var log;
				try {
				  log = hex ? hex : 'transparent';
				  if( opacity ) log += ', ' + opacity;
				} catch(e) {

				}
			},
			theme: 'default'
		});

		$('.system-color-picker').change(function() {
			var mainColor = $(this).val();
			var mainColorHover = generateColorShade(mainColor, -0.4);
			var mainColorLight = generateColorShade(mainColor, 0.9);
			var mainColorGradient = generateColorShade(mainColor, 0.2);
			var mainExtraLight = generateColorShade(mainColor, 0.93);
			var mainColorLightSecond = generateColorShade(mainColor, 0.94);
			var mainTextColor = isLightColor(mainColor) ? '#000000' : '#FFFFFF';
			var mainTextColorHover = isLightColor(mainColor) ? '#000000' : '#FFFFFF';
			var mainTextMenuColorHoverDefault = isLightColor(mainColor) ? '#FFFFFF' : '#000000';
			var gotopImgColor = mainTextColor == '#000000' ? 'invert(100%)' : 'invert(0)';
			var gotopHoverImgColor = mainTextColor == '#FFFFFF' ? 'invert(0)' : 'invert(100%)';
			var Whiteimagefilter = mainTextColor == '#FFFFFF' ? 'invert(100%)' : 'invert(0)';

			document.documentElement.style.setProperty('--mainColor', mainColor);
			document.documentElement.style.setProperty('--mainColorSecond', mainColor);
			document.documentElement.style.setProperty('--mainColorHover', mainColorHover);
			document.documentElement.style.setProperty('--gotopBGColor', mainColor);
			document.documentElement.style.setProperty('--gotopHoverBGColor', mainColorHover);
			document.documentElement.style.setProperty('--mainColorLight', mainColorLight);
			document.documentElement.style.setProperty('--mainColorGradient', mainColorGradient);
			document.documentElement.style.setProperty('--mainExtraLight', mainExtraLight);
			document.documentElement.style.setProperty('--mainColorLightSecond', mainColorLightSecond);

			document.documentElement.style.setProperty('--mainTextColor', mainTextColor);
		    document.documentElement.style.setProperty('--mainTextColorHover', mainTextColorHover);
		    document.documentElement.style.setProperty('--mainTextMenuColorHoverDefault', mainTextMenuColorHoverDefault);
		    document.documentElement.style.setProperty('--gotopImgColor', gotopImgColor);
		    document.documentElement.style.setProperty('--gotopHoverImgColor', gotopHoverImgColor);
		    document.documentElement.style.setProperty('--Whiteimagefilter', Whiteimagefilter);

			var rgbColor = hexToRgb(mainColor);
			var rgbColorHover = hexToRgb(mainColorHover);
			var filterColor = getFilterColor(rgbColor);
			var filterColorHover = getFilterColor(rgbColorHover);

			document.documentElement.style.setProperty('--mainfilterimg', filterColor);
			document.documentElement.style.setProperty('--mainfilterimgHover', filterColorHover);

			document.cookie = "SYSTEM_THEME_COLOR=" + mainColor; 
		});

		document.cookie = "ENABLE_COLOR_PICKER=Yes"; 
	});

	function getFilterColor(rgb) {
		const color = new Color(rgb[0], rgb[1], rgb[2]);
        const solver = new Solver(color);
        const result = solver.solve();

        return result.filter;
	}

	function generateColorShade(hexCode, adjustPercent) {
	    hexCode = hexCode.replace('#', '');

	    let hexArray = hexCode.match(/.{1,2}/g);
	    hexArray = hexArray.map(hex => parseInt(hex, 16));

	    hexArray.forEach((color, index) => {
	        let adjustableLimit = adjustPercent < 0 ? color : 255 - color;
	        let adjustAmount = Math.ceil(adjustableLimit * adjustPercent);

	        color = (color + adjustAmount).toString(16).padStart(2, '0');
	        hexArray[index] = color;
	    });

	    return '#' + hexArray.join('');
	}

	function isLightColor(color, lighterThan = 170) {
	    // Remove '#' if present
	    color = color.replace("#", "");

	    // Calculate RGB components
	    let r = parseInt(color.substr(0, 2), 16);
	    let g = parseInt(color.substr(2, 2), 16);
	    let b = parseInt(color.substr(4, 2), 16);

	    // Calculate luminance to determine if the color is light
	    return ((r * 299 + g * 587 + b * 114) / 1000) > lighterThan;
	}

</script>
<div class="colorpicker-container">
    <input type="text" id="system-color-picker" class="system-color-picker" data-letterCase="uppercase" value="<?= $SYSTEM_THEME_COLORS['MAIN_COLOR'] ?>">
</div>
<?php } ?>