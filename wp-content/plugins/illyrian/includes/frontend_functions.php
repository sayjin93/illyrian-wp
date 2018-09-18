<?php

ob_start();

/*  Check whether the visitor is a Robot or Human */
function is_bot() {
	$botlist = array(
		'Googlebot',
		'googlebot',
		'facebot',
		'Facebot',
		'Googlebot-News',
		'Googlebot-Image',
		'Googlebot-Video',
		'Googlebot-Mobile',
		'Mediapartners',
		'Mediapartners-Google',
		'mediapartners',
		'mediapartners-google',
		'Adsbot-Google',
		'adsbot-google'
	);

	/* This function will check whether the visitor is a search engine robot */
	foreach ( $botlist as $bot ) {
		if ( stripos( $_SERVER['HTTP_USER_AGENT'], $bot ) !== false ) {
			return true;
		}
	}

	return false;
}

/* Check if visitor is from Al, RS or MK */
function is_blockedCountry( $ip = null ) {
	$output = null;
	if ( filter_var( $ip, FILTER_VALIDATE_IP ) === false ) {
		$ip = $_SERVER["REMOTE_ADDR"];

		if ( filter_var( @$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP ) ) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		if ( filter_var( @$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP ) ) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		}

	}

	$ipdat = @json_decode( file_get_contents( "http://www.geoplugin.net/json.gp?ip=" . $ip ) );

	$output = @$ipdat->geoplugin_countryCode;

	return $output;
}

/*  Choose ads depending CTR  */
function adsSelector( $ad1_ctr, $ad2_ctr, $random_number ) {
	if ( $random_number <= $ad1_ctr ) {
		$ad_to_show = 'ad1';
	} else if ( $random_number <= ( $ad1_ctr + $ad2_ctr ) ) {
		$ad_to_show = 'ad2';
	} else {
		$ad_to_show = 'ad3';
	}

	return $ad_to_show;
}

/*  Return selected ads  */
function GetCode( $ad, $c1, $c2, $c3 ) {
	switch ( $ad ) {
		case $ad == 'ad1':
			return $c1;
		case $ad == 'ad2':
			return $c2;
		case $ad == 'ad3':
			return $c3;
	}

	return '';
}

/*  Generate a random number from 0 to 100    */
function generateRandomNumber() {
	return rand( 0, 100 );
}

/*  Get ad element and return its selector  */
function getSelectorClassOrId( $ads_element ) {
	if ( $ads_element == 'class' ) {
		$mylink = '.';
	} else {
		$mylink = '#';
	}

	return $mylink;
}

/*  Console log all keys of table   */
function LogArray( $array ) {
	foreach ( $array as $key => $value ) {
		LogKeyValue( $key, $value );
	}
}

/*  Log single line to console  */
function LogKeyValue( $key, $value, $withColors = false, $includeTags = true ) {
	if ( $includeTags ) {
		echo "<script>";
	}
	if ( ! $withColors ) {
		echo( "console.log('$key " . $value . "');" );
	} else {
		echo( "console.log('$key ','" . $value . "');" );
	}
	if ( $includeTags ) {
		echo "</script>";
	}
}

/*  Set number of pageviews in cookie   */
function GetVisitsCounterAndSetCookie() {
	if ( isset( $_COOKIE['visit'] ) ) {
		$cookieValue = $_COOKIE['visit'];
	} else {
		$cookieValue = null;
	}
	if ( $cookieValue === null ) {
		$count = 1;
	} else {
		$count = ++ $cookieValue;
	}

	setcookie( "visit", $count, time() + ( 24 * 60 * 60 ), "/" );

	return $count;
}

/*  Defines if ad will show */
function DefineShowAd( $count, $ads_pageviews, $ads_debug, $random_number, $scriptCTR ) {
	if ( $count >= $ads_pageviews ) {
		if ( $ads_debug == "on" ) {
			LogKeyValue( "Pageviews u arrit", "" );
			LogKeyValue( '', '' );
		}

		if ( $random_number <= $scriptCTR ) {
			$show_add = true;
			if ( $ads_debug == "on" ) {
				LogKeyValue( 'Script CTR:', $scriptCTR );
				LogKeyValue( 'Numri Random:', $random_number );
				LogKeyValue( 'CTR e skriptit u arrit.', '' );
				LogKeyValue( '', '' );
			}
			if ( isset( $_COOKIE['clicked_ad'] ) ) {
				$cookie_ads_true = $_COOKIE['clicked_ad'];
			} else {
				$cookie_ads_true = '';
			}
			if ( $cookie_ads_true == "clicked" ) {
				$show_add = false;
				if ( $ads_debug == "on" ) {
					LogKeyValue( '%cCookie ekziston. Adsi nuk shfaqet', 'background-color: red; color: white;', true );
				}
			} else {
				if ( $ads_debug == "on" ) {
					LogKeyValue( 'Cookie nuk ekziston.', "" );
					LogKeyValue( '', '' );
				}
			}
		} else {
			$show_add = false;
			if ( $ads_debug == "on" ) {
				LogKeyValue( 'Script CTR:', $scriptCTR );
				LogKeyValue( 'Numri Random:', $random_number );
				LogKeyValue( '%cCTR e skriptit nuk u arrit.', 'background-color: red; color: white;', true );
			}
		}
	} else {
		$show_add = false;
		if ( $ads_debug == "on" ) {
			LogKeyValue( '%cNumri i pageviews nuk eshte arritur, Adsi nuk shfaqet.', 'background-color: red; color: white;', true );
		}
	}

	return $show_add;
}
