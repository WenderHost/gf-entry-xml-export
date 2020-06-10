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

    if( stristr( $key, 'skill') ){
      /**
       * Spilt keys like `skill_1_id`, `skill_2_rating`, etc into an array as follows:
       * 0 - `skill`
       * 1 - Skill number
       * 2 - `id` or `rating`
       */
      $key_array = explode( '_', $key );
      if( is_array( $key_array ) && 3 == count( $key_array ) ){
        $data['skills']['skill' . $key_array[1] ][$key_array[2]] = $value;
      }

      // If we have both skill_X_id and skill_X_rating inside `skills`, we can delete the corresponding
      // `skill_X_id` and `skill_X_rating` from $data:
      $skill_check_array = $data['skills']['skill' . $key_array[1] ];
      if( array_key_exists( 'id', $skill_check_array) && array_key_exists( 'rating', $skill_check_array ) )
        unset( $data['skill_' . $key_array[1]. '_id'], $data['skill_' . $key_array[1]. '_rating'] );
    }

  }

  return $data;
}
add_filter( 'gf_to_xml_filter_data', 'digicube_filter_data' );

function digicube_filter_xml( $data ){
  $search = ['document0','document1','document2','document3','document4','document5','skill1','skill2','skill3','skill4','skill5','skill6'];
  $replace = ['document','document','document','document','document','document','skill','skill','skill','skill','skill','skill'];
  $data = str_replace( $search, $replace, $data );
  return $data;
}
add_filter( 'gf_to_xml_filter_xml', 'digicube_filter_xml' );