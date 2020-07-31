<?php if (!empty($this->image)): ?>

	<!-- This fieldset gets rendered if we have an image attached to the post: Replace -->
	<fieldset id="imageuploaddiv" style="margin:0;padding:0px;padding-top:1px;overflow:hidden;">
		<legend><?php _e('Yapb Image', 'yapb') ?></legend>
		<div style="background:url('<?php echo YAPB_PLUGIN_PATH ?>tpl/form-background.gif') repeat-x #F4F4F4;border:1px solid #B2B2B2;padding:10px;margin:0px;">
			<!-- Why can't browsers calculate the height of a div? -->
			<table border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td>
						<img src="<?php echo $this->image->getThumbnailHref(array('h=80','fltr[]=usm|30|3|3')) ?>" height="80" alt="" style="float:left;margin-right:10px;height:80px;">
						<?php _e('Replace image:', 'yapb') ?><input type="file" name="yapb_imageupload" size="30" tabindex="1" value="" id="imageupload" style="background-color:white;" /><br>
						<input type="checkbox" name="yapb_exifdate" id="checkbox_yapb_exifdate" value="1" <?php if(get_option('yapb_check_post_date_from_exif')): ?>checked<?php endif ?> /> <label for="checkbox_yapb_exifdate"><?php _e('Post date from image exif data if available', 'yapb') ?></label><br>
						<input type="checkbox" name="yapb_remove_image" value="1"> <span style="color:red;"><?php _e('Remove image from post', 'yapb') ?></span><br>
					</td>
				</tr>
			</table>
		</div>
	</fieldset>

<?php else: ?>

	<!-- This fieldset gets rendered if we have no image attached to the post: Upload -->
	<fieldset id="yapb_div_imageupload" style="margin:0;padding:0px;padding-top:1px;overflow:hidden;">
		<legend><?php _e('Yapb Image', 'yapb') ?></legend> 
		<div style="background:url('<?php echo YAPB_PLUGIN_PATH ?>tpl/form-background.gif') repeat-x #F4F4F4;border:1px solid #B2B2B2;padding:10px;margin:0px;">
			<input type="file" id="yapb_imageupload" name="yapb_imageupload" size="30" tabindex="1" value="" id="imageupload" style="background-color:white;" onChange="toggleCategory(true);" /><span class="submit"><input type="button" value="<?php _e('clear field', 'yapb') ?>" onClick="$('yapb_imageupload').value='';toggleCategory(false);" /></span><br>
			<input type="checkbox" name="exifdate" id="checkbox_yapb_exifdate" value="1" <?php if(get_option('yapb_check_post_date_from_exif')): ?>checked<?php endif ?> /> <label for="checkbox_yapb_exifdate"><?php _e('Post date from image exif data if available', 'yapb') ?></label><br>
		</div>
	</fieldset>

<?php endif;
