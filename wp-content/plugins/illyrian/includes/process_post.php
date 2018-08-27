<?php

function process_post() {
	$ads_active        = get_option( 'active' );
	$ads_debug         = get_option( 'debug' );
	$ads_element       = get_option( 'element' );
	$ads_element_value = get_option( 'element_value' );
	$scriptCTR         = get_option( 'scriptCTR' );
	$ads_pageviews     = get_option( 'num-of-pages' );
	$ad1_ctr           = get_option( 'limitAd1' );
	$ad2_ctr           = get_option( 'limitAd2' );
	$codeAd1           = stripslashes( get_option( 'codeAd1' ) );
	$codeAd2           = stripslashes( get_option( 'codeAd2' ) );
	$codeAd3           = stripslashes( get_option( 'codeAd3' ) );
	$ad3_ctr           = get_option( 'limitAd3' );
	$ads_time          = get_option( 'time' );
	$ads_opacity       = get_option( 'opacity' );
	$ads_custom_css    = get_option( 'custom_css' );

	if ( ( $ads_active == "yes" ) && is_single() && ! is_bot() ) {
		if ( $ads_debug == "off" ) { ?>
            <script>
                $(document).ready(function () {

                    /*Prevent Opening DevTools*/
                    $(document).keydown(function (event) {
                        if (event.keyCode === 123) {
                            return false;   //Prevent from f12
                        }
                        else if (event.ctrlKey && event.keyCode === 85) {
                            return false;  //Prevent from ctrl+u
                        }
                        else if (event.ctrlKey && event.shiftKey && event.keyCode === 73) {
                            return false;  //Prevent from ctrl+shift+i
                        }
                    });

                    /*Prevent Right Click*/
                    $(document).bind("contextmenu", function (e) {
                        e.preventDefault();
                    });

                    /*Remove ad_div when DevTools is open*/
                    window.addEventListener('devtoolschange', function (e) {
                        if (e.detail.open) {
                            $(".illyrian_div").remove();
                        }
                        else {
                            window.location.reload();
                        }
                    });

                    //Cross-browser, did not work in Undocked Devtool
                    jdetects.create(function (status) {
                        if (status === 'on')
                            $(".illyrian_div").remove();
                    });

                });
            </script>
			<?php
		} else {
			if ( is_bot() == true ) {
				echo "<script>console.log( 'Robots are not allowed!' );</script>";
			} else {
				echo "<script>console.log( 'Welcome human!' );</script>";
			}
		}

		if ( $ads_element_value != '' ) {

			/*Check if number of pageviews is reached*/
			$count = GetVisitsCounterAndSetCookie();

			if ( $ads_debug == "on" ) {
				LogKeyValue( "Kushti i pageviews:", $ads_pageviews );
				LogKeyValue( "Vizituar:", $count );
			}

			$random_number = generateRandomNumber();
			$show_add      = DefineShowAd( $count, $ads_pageviews, $ads_debug, $random_number, $scriptCTR );

			if ( $show_add == true ) {
				/*Element where ads will be place*/
				$mylink = getSelectorClassOrId( $ads_element );

				if ( $ads_debug == "on" ) {
					$toConsole = array(
						"Ad1 CTR:" => $ad1_ctr,
						"Ad2 CTR:" => $ad2_ctr,
						"Ad3 CTR:" => $ad3_ctr,
					);
					LogArray( $toConsole );
				}

				$rand_number = generateRandomNumber();
				$selected    = adsSelector( $ad1_ctr, $ad2_ctr, $rand_number );
				$codeToShow  = GetCode( $selected, $codeAd1, $codeAd2, $codeAd3 );
				?>

                <style type="text/css">
                    <?php echo $ads_custom_css; ?>
                </style>

                <div class="illyrian_div">
					<?php echo $codeToShow; ?>
                </div>

                <script>
                    jQuery(document).ready(function () {
                        if (RemoveIfNotGallery(<?php echo "'" . $mylink . $ads_element_value . "'" ?>)) {
                            return;
                        }

                        PositionAd('<?php echo $mylink . $ads_element_value ?>', '<?php echo $ads_opacity; ?>');

						<?php if ( $ads_debug == "on" ) {
						LogKeyValue( 'Randomi per adsin:', $rand_number, false, false );
						LogKeyValue( ( '%cShfaqet Adsi: ' . $selected ), 'background-color: green; color: white;', true, false );
					}?>

                        var cookie_ads_true = readCookie('clicked_ad');
                        if (cookie_ads_true === "clicked") {
                            jQuery('.illyrian_div').remove();
                        }

                    });

                    /* Creating cookie for click on ads */
                    jQuery(".illyrian_div").mouseover(function () {
						<?php if ( $ads_debug == "on" ) {
						echo 'console.log( "Mouse over Ads" );';
					}?>
                        PlaceCookieClicked(<?php echo $ads_time; ?>);
                    });

                </script>

				<?php
			} // if show_add == false
		} // end of main condition

		else {
			if ( $ads_debug == "on" ) {
				LogKeyValue( '%cSettings nuk jane plotesuar sic duhet. Scripti nuk eshte aktiv.', 'background-color: red; color: white;', true );
			}
		}
	} // if plugin is active
} // end function process_post

add_action( 'wp_head', 'process_post' );