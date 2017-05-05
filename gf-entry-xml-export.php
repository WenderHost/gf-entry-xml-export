<?php
/**
 * Plugin Name:     Gravity Forms Entry XML Export
 * Plugin URI:      https://github.com/WenderHost/gf-entry-xml-export
 * Description:     Writes out Gravity Forms entries as XML files
 * Author:          Michael Wender
 * Author URI:      https://michaelwender.com
 * Text Domain:     gf-entry-xml-export
 * Domain Path:     /languages
 * Version:         1.0.0
 *
 * @package         Gf_Entry_Xml_Export
 */

include_once 'lib/classes/array-to-xml.php';
include_once 'lib/fns/debug.php';
include_once 'lib/fns/files.php';
include_once 'lib/fns/gravityforms.php';
