<?php
if ( ! function_exists( 'write_log' ) )
{
    /**
     * Writes a log.
     *
     * @param      mixed  $log    Array or string to log.
     * @param      string $label  Label applied to the log output.
     */
    function write_log( $log, $label = '' )
    {
        if ( true === WP_DEBUG )
        {
            if( ! empty( $label ) )
                $label = '[' . $label . '] ';
            if ( is_array( $log ) || is_object( $log ) )
            {
                error_log( $label . print_r( $log, true ) );
            }
            else
            {
                error_log( $label . $log );
            }
        }
    }
}
?>