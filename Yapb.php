<?php

	/*	Plugin Name: Yet Another PhotoBlog 2
		Plugin URI: https://github.com/slick2/yet-another-photoblog-2
		Version: BETA 2
        Description: This Wordpress plugin enables, WordPress as a PhotoBlog. Continuation from the abandoned plugin by Johannes Jarolim.
        Author: Carey Dayrit
		Author URI: http://webtuners.pro
	*/

	/*  Copyright 2008 by J.P.Jarolim (email : yapb@johannes.jarolim.com)        

		This program is free software; you can redistribute it and/or modify
		it under the terms of the GNU General Public License as published by
		the Free Software Foundation; either version 2 of the License, or
		(at your option) any later version.

		This program is distributed in the hope that it will be useful,
		but WITHOUT ANY WARRANTY; without even the implied warranty of
		MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
		GNU General Public License for more details.

		You should have received a copy of the GNU General Public License
		along with this program; if not, write to the Free Software
		Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
	*/

	/*	Special Thanks:

		James Heinrich:       Shortly after finding phpThumb.sourceforge.net i threw away 
		                      my own on-request-thumbnailer. Same idea - But his project 
		                      is much more advanced and has much more features.
		Paul M. Jones:        Savant helps me alot seperating code and layout. This super-
		                      lightweight templating engine is a very very fine thing.
		VinayRas Infotech:    Thanks alot for providing such a fine small exif-toolbox
		                      to the community!
		DAVE:                 Dave recherched and posted the needed modifications for YAPB to 
		                      run under the new taxonomy scheme of WordPress 2.3+
		ALL DOWNLOADERS AND   Thank you all for the given feedback! Only thanks to the  
		FELLOW FORUM MEMBERS: provided feedback i were able to get this plugin that far.

	*/

	/* Short and sweet */

	require_once plugin_dir_path( __FILE__ ) . '/lib/Yapb.class.php';
	$yapb = new Yapb();


