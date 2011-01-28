<?php
/**
 * Ubertags CSS
 * 
 * @package Ubertags
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */
?>

#ubertags_search_container {
	width: 100%;
}

#ubertags_save_container {
	margin-top: 10px;
	width: 100%;
}

#ubertags_search_input {
	width: 80%;
	margin-top: 10px;
	display: inline;
	font-size: 1em;
}

#ubertags_search_submit {
	
}

span#ubertags_search_error {
	color: Red;
	font-size: 12px;
	font-weight: bold;
}

.ubertags_subtype_container {
	width: 46%;
	float: right;
	margin: 10px;
}


h3.ubertags_subtype_title {
	background: #E4E4E4;
	border-top-left-radius: 4px 4px;
	-webkit-border-radius: 4px;
	-moz-border-radius: 4px;
	-moz-box-shadow: 1px 1px 0px #999;
	-webkit-box-shadow: 1px 1px 0px #999;
	color: #333;
	padding: 5px 5px 3px;
}

div.ubertag_big_title {
	margin-top: 22px;
	height: 60px;
	display: block;
	font-size: 50px;
	color: #9D1520;
	width: 88%;
	text-align: left;
	margin-left: auto;
	margin-right: auto;
	font-weight: bold;
	text-shadow: 1px 1px 3px #000;
	font-style: italic;
}

div.ubertag_description {
	margin-left: auto;
	margin-right: auto;
	width: 88%;
	margin-bottom: 10px;
	color: #444;
	font-weight: bold;
}

div.ubertag_comment_block {
	float: right;
	font-size: 11px;
	color: #666;
}

div.enabled-content-type {
	float: left;
	margin-right: 12px;
	padding: 5px;
	-webkit-border-radius: 4px;
	-moz-border-radius: 4px;
	background: #cccccc;
}

div.enabled-content-type label{
	margin-right: 4px;
}

/** Loading box **/

.ubertags_subtype_container .ubertags_loading {
	border: 1px solid #666;
	background: #fff;
	padding-top: 10px;
	padding-bottom: 10px;
	margin-top: 15px;
	margin-bottom: 15px;
	width: 100%;
	-webkit-border-radius: 4px;
	-moz-border-radius: 4px;
	-webkit-box-shadow: 0 2px 5px rgba(0, 0, 0, 0.45);
	-moz-box-shadow: 0 2px 5px rgba(0, 0, 0, 0.45);
}

.ubertags_subtype_container .ubertags_loading img {
	display: block;
	margin-left: auto;
	margin-right: auto;
	margin-top: 7px;
	margin-bottom: 7px;
}

.ubertags_subtype_container .ubertags_loading p {
	text-align: center;
	font-weight: bold;
	color: #333;
}

/** Timeline  **/
#ubertag-timeline {
	height: 450px;
	border: 1px solid #aaa;
	/**overflow-x:hidden; overflow-y:scroll;**/
}


.timeline-event-label {
	padding-left: 5px;
	padding-top: 2px;
}


.timeline-event-bubble-image {
	float: none;
}