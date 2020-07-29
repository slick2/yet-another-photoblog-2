<?php

	// For cache-management purproses i instance a YAPB-Cache object
	require_once realpath(dirname(__file__) . '/../lib/YapbMaintainance.class.php');
	$maintainance = new YapbMaintainance();

	$message = null;

	// I don't believe it: The WP function "update_option" doesn't allow HTML
	// since it calls $wpdb->escape before updating an option
	// the code there preslashes all single quotes, double quotes and NUL bytes 
	// thus destroying html - now i've to replicate that function - 
	// what a bloody workaround
	function yapb_update_html_option($option_name, $newvalue) {

		global $wpdb;

		if (is_string($newvalue)) {
			$newvalue = trim($newvalue);
		}

		// If the new and old values are the same, no need to update.
		$oldvalue = get_option($option_name);
		if ($newvalue == trim($oldvalue)) {
			return false;
		}

		if (false === $oldvalue) {
			add_option($option_name, $newvalue);
			return true;
		}

		if (is_array($newvalue) || is_object($newvalue)) {
			$newvalue = serialize($newvalue);
		}

		// Somehow i get $_GET parameters preslashed by WordPress
		// I need HTML doublequotes inputs!
		// I have to do this before wp_cache_set since i don't
		// want the escaped double slashes to be cached either
		$newvalue = preg_replace('#\\\\"#', '"', $newvalue);

		wp_cache_set($option_name, $newvalue, 'options');

		// Since i take raw input for update i have to replace single quotes manually
		$newvalue = preg_replace("#'#", "\\'", $newvalue);

		$option_name = $wpdb->escape($option_name);
		$wpdb->query("UPDATE $wpdb->options SET option_value = '$newvalue' WHERE option_name = '$option_name'");
		if ( $wpdb->rows_affected == 1 ) {
			do_action("update_option_{$option_name}", array('old'=>$oldvalue, 'new'=>$_newvalue));
			return true;
		}
		return false;
	}

	/**
	 * This function decrypts the massive options-array 
	 * generated in Yapb.class.php and displays it as HTML
	 **/
	function generateOptionHTML($optionsArray) {

		foreach ($optionsArray as $optionGroup) {

			print '<div class="dbx-box">';
			print '<h3 class="dbx-handle">' . $optionGroup[0] . '</h3>';
			print '<div class="dbx-content">';
			if ($optionGroup[1] != '') print('<p><em>' . $optionGroup[1] . '</em></p>');

			foreach($optionGroup[2] as $optionSubGroup) {

				print '<fieldset class="options">';
				print '<legend>' . $optionSubGroup[0] . '</legend>';
				if ($optionSubGroup[1] != '') print('<p><em>' . $optionSubGroup[1] . '</em></p>');
				print '<ul style="list-style-type:none;">';

				foreach ($optionSubGroup[2] as $optionName => $optionArray) {

					print '<li>';
					
					$optionType = $optionArray[0];
					$optionText = $optionArray[1];

					global $wpdb;

					switch ($optionType) {

						case 'CHECKBOX' :

							print '<input type="checkbox" id="' . $optionName .'" name="' . $optionName . '" value="1" ' . (get_option($optionName) ? 'checked' : '') . ' /><label for="' . $optionName . '"> ' . $optionText . '</label>'; 
							break;

						case 'CHECKBOX_INPUT' :

							preg_match('/#([0-9]+)/', $optionText, $sizematch);
							$checkbox = '<input id="' . $optionName . '" type="checkbox" name="' . $optionName . '_activate" value="1" ' . (get_option($optionName . '_activate') ? 'checked' : '') . ' /> ';
							$inputField = '<input type="text" size="' . $sizematch[1] . '" name="' . $optionName . '" value="' . get_option($optionName) . '" /> ';
							print $checkbox . '<label for="' . $optionName . '">' . preg_replace('/#[0-9]+/', '</label>' . $inputField, $optionText);
							break;

						case 'CHECKBOX_SELECT' :

							$optionFields = $optionArray[2];
							$checkbox = '<input type="checkbox" name="' . $optionName . '_activate" value="1" ' . (get_option($optionName . '_activate') ? 'checked' : '') . ' /> ';
							$selectField = '<select name="' . $optionName . '">';
							foreach ($optionFields as $key => $value) {
								$selectField .= '<option value="' . $value . '" ' . (($value == get_option($optionName))?'selected':'') . '>' . $key . '</option>';
							}
							$selectField .= '</select>';
							print $checkbox . preg_replace('/#/', $selectField, $optionText);
							break;

						case 'SELECT' :

							$optionFields = $optionArray[2];
							$selectField = '<select name="' . $optionName . '">';
							foreach ($optionFields as $key => $value) {
								$selectField .= '<option value="' . $value . '" ' . (($value == get_option($optionName))?'selected':'') . '>' . $key . '</option>';
							}
							$selectField .= '</select>';
							print preg_replace('/#/', $selectField, $optionText);
							break;

						case 'INPUT' :

							preg_match('/#([0-9]+)/', $optionText, $match);
							$input = '<input type="text" size="' . $match[1] . '" name="' . $optionName . '" value="' . get_option($optionName) . '" />';
							print preg_replace('/#[0-9]+/', $input, $optionText);
							break;

						case 'TEXTAREA' :

							print $optionText . '<br />';
							print '<textarea name="' . $optionName . '" style="width:98%;" rows="3" cols="50">' . get_option($optionName) . '</textarea>';
							break;
						
						case 'CUSTOM_VIEW_EXIF_TAGNAMES' :

							print $optionText . '<br />';
							print '<table border="0" style="background-color:#F4F4F4;border:1px solid #B2B2B2;padding:10px;margin:0px;margin-top:10px;">';
							print '<tr>';
							print '<td valign="top" nowrap>';
					
							$allLearnedTagnames = ExifUtils::getLearnedTagnames();
							$selected = explode(',', get_option($optionName));

							$i=0;
							$count = count($allLearnedTagnames);

							if ($count > 0) {

								for ($i; $i<$count; $i++) {
									print '<input type="checkbox" name="' . $optionName . '[]" value="' . $allLearnedTagnames[$i] . '"' . ((in_array($allLearnedTagnames[$i], $selected))?' checked':'') . ' /> ' . $allLearnedTagnames[$i] . '<br>';
									if (($i + 1) % ($count/4) == 0) print '</td><td style="padding-left:20px;" valign="top">';
								}
								if (($i + 1) % ($count/4) == 0) print '</td>';

							} else {

								print('Please post at least one image containing EXIF data so YAPB can learn which EXIF tags your camera uses.');

							}

							print '</tr>';
							print '</table>';

					}

					print '</li>';

				}

				print '</ul>';
				print '</fieldset>';

			}

			print '</div>';
			print '</div>';

		}
	}

	// Code which reacts on changes
	$action = isset($_POST['action']) 
		? $_POST['action'] 
		: '';

	if ($action == 'update') {
		
		foreach ($this->options as $optionGroup) {
			foreach($optionGroup[2] as $optionSubGroup) {
				foreach ($optionSubGroup[2] as $optionName => $optionArray) {
					switch ($optionArray[0]) {
						
						case 'CHECKBOX_INPUT' :
						case 'CHECKBOX_SELECT' :

							// update activation checkbox
							if (isset($_POST[$optionName.'_activate'])) update_option($optionName.'_activate', $_POST[$optionName.'_activate']);
							else update_option($optionName.'_activate', '');

							// update value
							if (isset($_POST[$optionName])) yapb_update_html_option($optionName, $_POST[$optionName]);
							else update_option($optionName, '');

							break;

						case 'SELECT' :
						case 'CHECKBOX' :
						case 'INPUT' :
						case 'TEXTAREA' :

							if (isset($_POST[$optionName])) yapb_update_html_option($optionName, $_POST[$optionName]);
							else update_option($optionName, '');
							break;
						
						case 'CUSTOM_VIEW_EXIF_TAGNAMES' :
							
							if (isset($_POST[$optionName])) {
								$temp = $_POST[$optionName];
								update_option($optionName, implode(',', $temp));
							} else update_option($optionName, '');
							break;

					}
				}
			}
		}

		$message = __('Options Updated.', 'yapb');

	} else 

	if ($action == 'clear_cache') {

		$maintainance->clearCache();
		$message = __('Cache cleared', 'yapb');

	} else 

	if ($action == 'hide_sponsoring_message') {

		update_option('yapb_hide_sponsoring_message', 1);

	}

?>

<?php if (!is_null($message)) : ?>
	<div id="message" class="updated fade"><p><?php echo $message ?></p></div>
<?php endif; ?>

<style type="text/css">

	/* hide the quick tags */
	#quicktags { display: none; }

	/* toggle dbx images */
	a.dbx-toggle, a.dbx-toggle:visited {
		display:block;
		overflow: hidden;
		background-image: url(images/toggle.gif);
		position: absolute;
		top: 0px;
		right: 0px;
		background-repeat: no-repeat;
		border: 0px;
		margin: 0px;
		padding: 0px;
	}
			
	a.dbx-toggle, #moremeta a.dbx-toggle-open:visited {
		height: 25px;
		width: 27px;
		background-position: 0 0px;
	}

	a.dbx-toggle-open, #moremeta a.dbx-toggle-open:visited {
		height: 25px;
		width: 27px;
		background-position: 0 -25px;
	}

	#grabit {
		width: 100%;
	}

	.dbx-box {
		margin-bottom:10px;
	}

	.dbx-box-open .dbx-handle {
		background-color:#14568A;
	}

	.dbx-box-closed .dbx-handle {
		background-color:#6DA6D1;
	}

	.dbx-content {
		padding:10px;
		border-bottom:1px solid #14568A;
		border-left:1px solid #14568A;
		border-right:1px solid #14568A;
	}

</style>

<!-- Script which initializes the dbx-groups, etc. -->
<script type="text/javascript" src="<?php echo YAPB_PLUGIN_PATH ?>js/dbx-options-panel.js"></script>

<div class="wrap">

	<?php global $yapb ?>

	<h2><?php printf(__('Yet Another Photoblog %s', 'yapb'), $this->yapbVersion) ?></h2>
	
	<div style="margin-bottom:2em;">

		<div style="float:right;margin-left:2em; border:1px solid silver; padding:1em;width:400px;">
			<h2 style="background-image:url('<?php echo YAPB_PLUGIN_PATH ?>tpl/heading-bg.gif');">YAPB Support</h2>
			<p>There's a growing community using YAPB to publish their photos via WordPress - Don't hesitate to ask for help or share your knowledge in the YAPB-Forum: <a href="http://johannes.jarolim.com/yapb-forum" target="_blank">http://johannes.jarolim.com/yapb-forum</a></p>

			<h2 style="margin-top:1em;background-image:url('<?php echo YAPB_PLUGIN_PATH ?>tpl/heading-bg.gif');">Support YAPB</h2>
				<p>
					<strong>Do you like YAPB?</strong> Do you use it regulary to show your photos or images? Did YAPB save you time? Or you just want to give something back for the time spent to create, maintain and support YAPB? <strong>Just Donate a little ammount</strong> so i may buy a good book, DVD, etc. or just pay some server traffic:
				</p>
				<p>
					<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
						<input type="hidden" name="cmd" value="_xclick">
						<input type="hidden" name="business" value="paypal@johannes.jarolim.com">

						<input type="hidden" name="item_name" value="A donation for Yet Another Photoblog">
						<input type="hidden" name="item_number" value="1">
						<input type="hidden" name="no_shipping" value="2">
						<input type="hidden" name="no_note" value="1">
						<input type="hidden" name="currency_code" value="EUR">
						<input type="hidden" name="tax" value="0">
						<input type="hidden" name="lc" value="AT">
						<input type="hidden" name="bn" value="PP-DonationsBF">
						<input type="image" src="/blog/wp-content/uploads/Image/common/paypal-donate.gif" border="0" name="submit" alt="Donate with PayPal - fast, free and secure!">
						<img alt="" border="0" src="https://www.paypal.com/de_DE/i/scr/pixel.gif" width="1" height="1">
					</form>
				</p>
				<p>
					Thanks a lot from Salzburg!
				</p>
		</div>


		<?php
			
			// Since i'm not able to cleanly remove all division by zero
			// bugs in this script, i'm removing all divisions with this function ;-)
			function saveDiv($param1, $param2) {
				$result = 0;
				if ($param2 != 0) {
					$result = $param1 / $param2;
				}
				return $result;
			}

			$imagefileCount = $maintainance->getImagefileCount();
			$imagefileSize = $maintainance->getImagefileSizeBytes();
			$cachefileCount = $maintainance->getCachefileCount();
			$cachefileSize = $maintainance->getCachefileSizeBytes(); 

			$averageImagefileSize = round(saveDiv($imagefileSize, $imagefileCount), 2);

			function strong($text) {
				return '<strong>' . $text . '</strong>';
			}
			
			function sizePresentation($sizeInBytes) {
				if ($sizeInBytes < 1048576) {
					return strong(round($sizeInBytes / 1024, 0)) . ' KB';
				} else {
					return strong(round($sizeInBytes / 1024 / 1024, 1)) . ' MB';
				}
			}



		?>

		<style type="text/css">
			.big {
				font-size:2em;
				font-weight:normal;
				color:black;
			}
		</style>

		<span class="big">
			<strong><?php echo $imagefileCount ?></strong> <?php _e('Images', 'yapb') ?>
		</span>

		<ul>
			<li><?php printf(__('You have posted %s YAPB-Images with an overall size of %s.', 'yapb'), strong($imagefileCount), sizePresentation($imagefileSize)) ?></li>
			<?php if ($imagefileCount > 0): ?><li><?php printf(__('In average, an image needs %s of disk space.', 'yapb'), sizePresentation(saveDiv($imagefileSize, $imagefileCount))) ?></li><?php endif ?>
		</ul>

		<span class="big">
			<strong><?php echo $cachefileCount ?></strong> <?php _e('Thumbnails','yapb') ?><br />
		</span>
				
		<?php if ($cachefileCount > 0): ?>
			<ul>
				<li><?php printf(__('Currently, the cache contains %s thumbnails with a overall size of %s.', 'yapb'), $cachefileCount, sizePresentation($cachefileSize)) ?></li>
				<li><?php printf(__('In average, a thumbnail needs %s of disk space.', 'yapb'), sizePresentation(saveDiv($cachefileSize, $cachefileCount))) ?></li>
			</ul>
		<?php else: ?>
			<ul>
				<li>no thumbnails were generated yet.</li>
			</ul>
		<?php endif ?>

		<span class="big">
			<?php echo sizePresentation($imagefileSize+$cachefileSize) ?>
		</span>

		<ul>
			<li><?php printf(__('Currently, YAPB consumes %s of disk space for images.', 'yapb'), sizePresentation($imagefileSize + $cachefileSize)) ?></li>
			<?php if ($cachefileCount > 0): ?>
				<li><?php printf(__('In average, %s thumbnails per image were generated.', 'yapb'), strong(round(saveDiv($cachefileCount, $imagefileCount), 2))) ?></li>
				<li><?php printf(__('In average, one posted image incl. all associated thumbnails needs approx. %s of disk space.', 'yapb'), sizePresentation(saveDiv($cachefileCount, $imagefileCount) * saveDiv($cachefileSize, $cachefileCount) + saveDiv($imagefileSize, $imagefileCount))) ?></li>
			<?php endif; ?>
		</ul>

		<div style="clear:right;"><!-- clear --></div>

	</div>

	<form method="post" action="<?php echo $_SERVER['REQUEST_URI'] ?>">

		<input type="hidden" name="page" value="<?php echo $_GET['page'] ?>"> 
		<input type="hidden" name="action" value="update" /> 

		<div class="dbx-group" id="yapb-options">
			<?php generateOptionHTML($this->options) ?>
		</div>

		<p class="submit">
			<input type="submit" name="Submit" value="<?php _e('Update Options') ?> &raquo;" /> 
		</p>

	</form>

	<h2><?php _e('Maintainance', 'yapb') ?></h2>

	<div class="dbx-group" id="yapb-cache-toolbox">

		<div class="dbx-box">
			<h3 class="dbx-handle"><?php _e('Cache Maintainance', 'yapb') ?></h3>
			<div class="dbx-content">
				<p><?php _e('From time to time you may want to tidy up your thumbnail cache:', 'yapb') ?></p>
				<form method="post" action="<?php echo $_SERVER['REQUEST_URI'] ?>">

					<input type="hidden" name="page" value="<?php echo $_GET['page'] ?>"> 
					<input type="hidden" name="action" value="clear_cache" /> 
					<input type="submit" name="clear" value="<?php _e('Clear cache','yapb') ?>" />

				</form>
			</div>
		</div>
	</div>

</div>