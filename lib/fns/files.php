<?php
namespace GFtoXML\files;

function write_file( $file_contents = '', $filename = 'file.txt', $dir = 'tmp' )
{
    $upload_dir = \wp_upload_dir();
    $target_dir = \trailingslashit( $upload_dir['basedir'] . '/' . $dir );

    $access_type = \get_filesystem_method();
    if( 'direct' === $access_type )
    {
        $creds = \request_filesystem_credentials( site_url() . '/wp-admin/' );

        // break if we find any problems
        if( ! \WP_Filesystem( $creds ) )
        {
            write_log( 'Unable to get filesystem credentials.', 'GFtoXML::' . basename(__FILE__) . '::' . __LINE__ );
            return false;
        }

        global $wp_filesystem;

        // Check/Create /uploads/tmp/
        if( ! $wp_filesystem->is_dir( $target_dir ) )
            $wp_filesystem->mkdir( $target_dir );

        if( ! $wp_filesystem->is_dir( $target_dir ) )
        {
            write_log( 'Unable to create XML directory (' . $target_dir . ').', 'GFtoXML::' . basename(__FILE__) . '::' . __LINE__ );
            return false;
        }

        // Prevent web access via .htaccess
        $ht_file = $target_dir . '.htaccess';
        if( ! $wp_filesystem->exists( $ht_file ) ){
            write_log( 'Writing .htaccess file...', 'GFtoXML::' . basename(__FILE__) . '::' . __LINE__ );
            $contents = "# Prevent access\nDeny from all";
            $wp_filesystem->put_contents( $ht_file, $contents, 0444 );
        }

        $file_to_write = $target_dir . $filename;
        write_log( 'Writing file ' . $file_to_write, 'GFtoXML::' . basename(__FILE__) . '::' . __LINE__ );
        $wp_filesystem->put_contents( $file_to_write, $file_contents );
    }
}