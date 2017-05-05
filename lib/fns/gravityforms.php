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
    // Build the string for our $lead_source
    $search = ['http://','https://','/'];
    $site_url = str_replace( $search, '', site_url( '', '' ) );
    $lead_source = $site_url . ' - ' . $form['title'];

    // Initialize array which will hold our form data
    $data = array();
    $data['time'] = $entry['date_created'];
    $data['lead_source'] = $lead_source;

    // Map form data to an array
    foreach( $form['fields'] as $field )
    {
        if( isset( $field['inputs'] ) && is_array( $field['inputs'] ) )
        {
            foreach( $field['inputs'] as $input )
            {
                if( isset( $input['isHidden'] ) && true == $input['isHidden'] )
                    continue;

                $label = $input['label'];
                $value = $entry[$input['id']];
                $data[$label] = $value;
            }
        }
        else
        {
            $label = $field['label'];
            $value = $entry[$field['id']];
            $data[$label] = $value;
        }
    }

    // Rename keys
    $new_keys = [
        'First' => 'first_name',
        'Last' => 'last_name',
        'Cancer Diagnosis:' => 'type_of_cancer',
        'Cancer Diagnosis or Additional Comments:' => 'comments',
        'How can we help?' => 'request_type',
    ];

    foreach( $data as $key => $value ){
        if( array_key_exists( $key, $new_keys ) ){
            $data[$new_keys[$key]] = $value;
            unset( $data[$key] );
        }
    }

    // Generate XML
    $xml = \GFtoXML\ArrayToXml\ArrayToXml::convert( $data, 'form' );
}
add_action( 'gform_after_submission', __NAMESPACE__ . '\\gforms_after_submission', 10, 2 );
