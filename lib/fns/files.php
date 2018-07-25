<?php
namespace GFtoXML\files;

function write_file( $file_contents = '', $filename = 'file.txt', $dir = 'tmp' )
{
    $upload_dir = \wp_upload_dir();
    $target_dir = \trailingslashit( $upload_dir['basedir'] . '/' . $dir );

    if( ! function_exists( '\get_filesystem_method' ) )
        require_once ABSPATH . 'wp-admin/includes/file.php';

    $access_type = \get_filesystem_method();
    if( 'direct' === $access_type )
    {
        $creds = \request_filesystem_credentials( site_url() . '/wp-admin/' );

        // break if we find any problems
        if( ! \WP_Filesystem( $creds ) )
        {
            write_log( 'Unable to get filesystem credentials.' );
            return false;
        }

        global $wp_filesystem;

        // Check/Create /uploads/tmp/
        if( ! $wp_filesystem->is_dir( $target_dir ) )
            $wp_filesystem->mkdir( $target_dir );

        if( ! $wp_filesystem->is_dir( $target_dir ) )
        {
            write_log( 'Unable to create XML directory (' . $target_dir . ').' );
            return false;
        }

        // Prevent web access via .htaccess
        $ht_file = $target_dir . '.htaccess';
        if( ! $wp_filesystem->exists( $ht_file ) ){
            write_log( 'Writing .htaccess file...' );
            $contents = "# Prevent access\nDeny from all";
            $wp_filesystem->put_contents( $ht_file, $contents, 0444 );
        }

        $file_to_write = $target_dir . $filename;
        write_log( 'Writing file ' . $file_to_write );
        $wp_filesystem->put_contents( $file_to_write, $file_contents );
    } else {
        write_log( 'ERROR: Unable to write file (' . $filename . '). $access_type = ' . $access_type );
        if( defined( 'GFTOXML_ADMIN_EMAIL' ) )
            wp_mail( GFTOXML_ADMIN_EMAIL, 'GFtoXML - Unable to write file', 'The GFtoXML Plugin was unable to write the file ('.$filename.'). The $access_type = ' . $access_type );
    }
}