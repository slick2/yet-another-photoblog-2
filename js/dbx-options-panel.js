//initialise the docking boxes manager
var yapbManager = new dbxManager('yapb-options'); 	//session ID [/-_a-zA-Z0-9/]
var yapbCacheManager = new dbxManager('yapb-cache-toolbox');

function initDbx() {
	//create new docking boxes group
	var yapbGroup = new dbxGroup(
		'yapb-options', // container ID [/-_a-zA-Z0-9/]
		'vertical', 	// orientation ['vertical'|'horizontal']
		'7', 			// drag threshold ['n' pixels]
		'yes',			// restrict drag movement to container axis ['yes'|'no']
		'10', 			// animate re-ordering [frames per transition, or '0' for no effect]
		'yes', 			// include open/close toggle buttons ['yes'|'no']
		'open', 		// default state ['open'|'closed']

		'open', 		// word for "open", as in "open this box"
		'close', 		// word for "close", as in "close this box"
		'click-down and drag to move this box', // sentence for "move this box" by mouse
		'click to %toggle% this box', // pattern-match sentence for "(open|close) this box" by mouse
		'use the arrow keys to move this box', // sentence for "move this box" by keyboard
		', or press the enter key to %toggle% it',  // pattern-match sentence-fragment for "(open|close) this box" by keyboard
		'%mytitle%  [%dbxtitle%]' // pattern-match syntax for title-attribute conflicts
	);

	var yapbCacheGroup = new dbxGroup(
		'yapb-cache-toolbox', // container ID [/-_a-zA-Z0-9/]
		'vertical', 	// orientation ['vertical'|'horizontal']
		'7', 			// drag threshold ['n' pixels]
		'yes',			// restrict drag movement to container axis ['yes'|'no']
		'10', 			// animate re-ordering [frames per transition, or '0' for no effect]
		'yes', 			// include open/close toggle buttons ['yes'|'no']
		'open', 		// default state ['open'|'closed']

		'open', 		// word for "open", as in "open this box"
		'close', 		// word for "close", as in "close this box"
		'click-down and drag to move this box', // sentence for "move this box" by mouse
		'click to %toggle% this box', // pattern-match sentence for "(open|close) this box" by mouse
		'use the arrow keys to move this box', // sentence for "move this box" by keyboard
		', or press the enter key to %toggle% it',  // pattern-match sentence-fragment for "(open|close) this box" by keyboard
		'%mytitle%  [%dbxtitle%]' // pattern-match syntax for title-attribute conflicts
	);

}

Event.observe(window, 'load', initDbx, false);