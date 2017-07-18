# Gravity Forms Entry XML Export #
**Contributors:** thewebist  
**Tags:** gravityforms, xml  
**Requires at least:** 3.7  
**Tested up to:** 4.7.4  
**Stable tag:** 1.0.1  
**License:** GPLv2 or later  
**License URI:** http://www.gnu.org/licenses/gpl-2.0.html  

Writes out Gravity Forms submissions as XML files.

## Description ##

This plugin saves out all your Gravity Forms entries as XML files.

Should you want to rename the XML tags generated by your form, you can use the filter `gf_to_xml_array_keys` which supplies a form submission mapped to an array along with the form ID. Example:

```
function new_keys( $new_keys, $form_id )
{
    switch( $form_id )
    {
        default:
            $new_keys['First'] = 'first_name';
            $new_keys['Last'] = 'last_name';
        break;
    }
    
    return $new_keys;
}
add_filter( 'gf_to_xml_array_keys', 'new_keys', 10, 2 );
```

## Installation ##

1. Upload the plugin to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

## Changelog ##

### 1.0.1 ###
* Adding sitename to XML export filename

### 1.0.0 ###
* Initial release
