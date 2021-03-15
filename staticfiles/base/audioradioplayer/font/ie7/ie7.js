/* To avoid CSS expressions while still supporting IE 7 and IE 6, use this script */
/* The script tag referencing this file must be placed before the ending body tag. */

/* Use conditional comments in order to target IE 7 and older:
	<!--[if lt IE 8]><!-->
	<script src="ie7/ie7.js"></script>
	<!--<![endif]-->
*/

(function() {
	function addIcon(el, entity) {
		var html = el.innerHTML;
		el.innerHTML = '<span style="font-family: \'ds\'">' + entity + '</span>' + html;
	}
	var icons = {
		'icon-shuffle2': '&#xe90d;',
		'icon-repeat2': '&#xe90e;',
		'icon-audio': '&#xe90f;',
		'icon-download': '&#xe90c;',
		'icon-spotify': '&#xe900;',
		'icon-soundcloud': '&#xe901;',
		'icon-apple': '&#xe902;',
		'icon-amazon': '&#xe903;',
		'icon-cart': '&#xe904;',
		'icon-twitter': '&#xe905;',
		'icon-email': '&#xe906;',
		'icon-facebook': '&#xe907;',
		'icon-mute': '&#xe908;',
		'icon-volume': '&#xe909;',
		'icon-shuffle': '&#xe90a;',
		'icon-repeat': '&#xe90b;',
		'0': 0
		},
		els = document.getElementsByTagName('*'),
		i, c, el;
	for (i = 0; ; i += 1) {
		el = els[i];
		if(!el) {
			break;
		}
		c = el.className;
		c = c.match(/icon-[^\s'"]+/);
		if (c && icons[c[0]]) {
			addIcon(el, icons[c[0]]);
		}
	}
}());
