<?php
/**
 * Plugin Name:     Gravity Forms Entry XML Export for Provision IMS
 * Plugin URI:      https://github.com/mwender
 * Description:     Writes out Gravity Forms entries as XML files for later import into Provision Healthcare's Intake Management System
 * Author:          Michael Wender
 * Author URI:      https://michaelwender.com
 * Text Domain:     gf-entry-xml-export
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Gf_Entry_Xml_Export
 */

include_once 'lib/classes/array-to-xml.php';
include_once 'lib/fns/debug.php';
include_once 'lib/fns/gravityforms.php';
