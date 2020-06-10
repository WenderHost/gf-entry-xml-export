<?php
/**
 * Plugin Name:     Gravity Forms Entry XML Export
 * Plugin URI:      https://github.com/WenderHost/gf-entry-xml-export
 * Description:     Writes out Gravity Forms entries as XML files
 * Author:          Michael Wender
 * Author URI:      https://michaelwender.com
 * Text Domain:     gf-entry-xml-export
 * Domain Path:     /languages
 * Version:         1.2.2
 *
 * @package         Gf_Entry_Xml_Export
 */

include_once 'lib/classes/array-to-xml.php';
include_once 'lib/fns/debug.php';
include_once 'lib/fns/files.php';
include_once 'lib/fns/gravityforms.php';

function digicube_filter_data( $data ){
  foreach ($data as $key => $value) {
    switch( $key ){
      case 'document':
        $eval_value = eval( 'return ' . $value . ';' );
        unset( $data['document'] );
        if( is_array( $eval_value ) ){
          $data['documents'] = [];
          $x = 0;
          foreach ( $eval_value as $document ) {
            $data['documents']['document' . $x ] = stripslashes( $document );
            $x++;
          }
        }
        break;

      case 'startDate':
        $date = new DateTime( $value );
        $data[$key] = $date->format('d.m.Y');
        break;
    }
  }

  return $data;
}
add_filter( 'gf_to_xml_filter_data', 'digicube_filter_data' );

function digicube_filter_xml( $data ){
  $search = ['document0','document1'];
  $replace = ['document','document'];
  $data = str_replace( $search, $replace, $data );
  return $data;
}
add_filter( 'gf_to_xml_filter_xml', 'digicube_filter_xml' );