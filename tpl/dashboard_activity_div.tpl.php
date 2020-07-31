<div>
	<h3>Yet Another Photoblog <?php echo $this->pluginVersion ?></h3>

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

		$maintainance = $this->yapbMaintainanceInstance;

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
			font-size:1.2em;
			font-weight:bold;
			color:gray;
			margin-top:10px;
		}
	</style>

	<p>
		<?php printf(__('You have posted %s YAPB-Images with an overall size of %s.', 'yapb'), strong($imagefileCount), sizePresentation($imagefileSize)) ?> <?php if ($imagefileCount > 0): ?><?php printf(__('In average, an image needs %s of disk space.', 'yapb'), sizePresentation(saveDiv($imagefileSize, $imagefileCount))) ?><?php endif ?> <a href="options-general.php?page=Yapb.class.php">more info &raquo;</a>
	</p>

</div>