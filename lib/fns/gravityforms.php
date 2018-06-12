<?php
namespace GFtoXML\gravityforms;

/**
 * Handles writing out GF form entries as XML.
 *
 * @param      array  $entry  The entry
 * @param      array  $form   The form
 */
function gforms_after_submission( $entry, $form )
{
    $url = get_home_url();
    $url_search = ['www.','http://','https://','.com','.net','.org','.biz','.us'];
    $sitename = str_replace( $url_search, '', $url );
    $sitename = str_replace( '.', '-', $sitename );
    $filename = $sitename . '_form-' . $entry['form_id'] . '_entry-' . $entry['id'] . '.xml';
    $directory = 'xml';

    // Initialize array which will hold our form data
    $data = array();
    $data['time'] = date( 'c', strtotime( $entry['date_created'] ) );


    // Map form data to an array
    foreach( $form['fields'] as $field )
    {
        if( isset( $field['inputs'] ) && is_array( $field['inputs'] ) )
        {
            foreach( $field['inputs'] as $input )
            {
                if( isset( $input['isHidden'] ) && true == $input['isHidden'] )
                    continue;

                $label = ( isset( $input['adminLabel'] ) && ! empty( $input['adminLabel'] ) )? $input['adminLabel'] : $input['label'];
                $value = $entry[$input['id']];
                $data[$label] = $value;
            }
        }
        else
        {
            /**
             * Special processing for hidden fields
             *
             * Use the hidden field's label to trigger special processing:
             *
             * createdirectory - Use the value of the hidden field to save the form's
             *   entries in a dir of the same name
             */
            if( 'hidden' == $field['type'] ){
              switch( strtolower( $field['label'] ) ){
                case 'createdirectory':
                case 'createdir':
                  $directory.= '/' . sanitize_title( $entry[$field['id']], 'undefined' );
                  break;

                default:
                    // nothing
              }
            }

            $label = ( isset( $field['adminLabel'] ) && ! empty( $field['adminLabel'] ) )? $field['adminLabel'] : $field['label'];
            $value = $entry[$field['id']];
            $data[$label] = $value;
            if( 'page_title' == $field['label'] )
                $page_title = $value;
            if( isset( $field['cssClass'] ) && ! empty( $field['cssClass'] ) ){
                $data['cssClasses'][$label] = $field['cssClass'];
            }
        }
    }

    /**
     * Build the $lead_source string.
     *
     * Format: SITE_URL - PAGE_TITLE
     */
    $lead_source = [];

    // Add $site_url to $lead_source
    $search = ['http://','https://','/'];
    $site_url = str_replace( $search, '', site_url( '', '' ) );
    $lead_source[] = $site_url;

    // Add $page_title to $lead_source
    if( isset( $page_title ) && ! empty( $page_title ) )
        $lead_source[] = $page_title;

    $lead_source = implode( ' - ', $lead_source );

    // Add the $lead_source to our form data array that we'll
    // use to build the XML
    $data['lead_source'] = $lead_source;

    /**
     * Rename keys
     *
     * @filter        gf_to_xml_array_keys
     */
    $new_keys = [];
    $new_keys = apply_filters( 'gf_to_xml_array_keys', $new_keys, $entry['form_id'] );
    if( ! empty( $new_keys ) && is_array( $new_keys ) )
    {
        foreach( $data as $key => $value ){
            if( array_key_exists( $key, $new_keys ) ){
                $data[$new_keys[$key]] = $value;
                unset( $data[$key] );
            }
        }
    }

    /**
     * Process CSS Classes
     *
     * Utilize CSS classes for special operations. Current options:
     *
     * createdirectory - creates a directory using the field's submitted value as the directory name
     */
    if( isset( $data['cssClasses'] ) && is_array( $data['cssClasses'] ) ){
      foreach ($data['cssClasses'] as $label => $class ) {
        switch( $class ){
          case 'createdir':
          case 'createdirectory':
            if( 'xml' == $directory ) // check to see if dir hasn't already been changed
              $directory.= '/' . sanitize_title( $data[$label], 'undefined' );
            break;

          default:
            // nothing
            break;
        }
      }

      unset( $data['cssClasses'] );
    }

    // Generate XML
    $xml = \GFtoXML\ArrayToXml\ArrayToXml::convert( $data, 'form' );
    \GFtoXML\files\write_file( $xml, $filename, $directory );
}
add_action( 'gform_after_submission', __NAMESPACE__ . '\\gforms_after_submission', 10, 2 );
