<?php

	class YapbUtils {

		var $info = 'Class YapbUtils (C) 2006 by J.P.Jarolim';

		public function __construct(){
			$this->YapbUtils();
			
		}

		/** constructor **/
		function YapbUtils() {}

		/**
		 * this method escapes a string to be usable in javascript strings
		 *
		 * @param string $string
		 * @return string
		 */
		public static function escape($string) {
			$search = array(
				"/'/",
				"/[\r\n]+/",
				"/\t/"
			);
			$replace = array(
				"\\'",
				"",
				""
			);
			return preg_replace($search, $replace, $string);
		}

	}

?>
