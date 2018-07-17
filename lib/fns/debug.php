<?php
if ( ! function_exists( 'write_log' ) )
{
    /**
     * Writes a log.
     *
     * @param      mixed  $log    Array or string to log.
     * @param      string $label  Label applied to the log output.
     */
    function write_log( $log )
    {
        if ( true === WP_DEBUG )
        {
            $last = [];
            $label = '';
            $backtrace = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS );
            foreach( $backtrace as $trace ){
                if( stristr( $trace['file'], 'gf-entry-xml-export') ){
                    $last = $trace;
                    break;
                }
            }

            if( 0 < count( $last ) )
                $label = '[GFtoXML::'.basename($last['file']).'::'.$last['line'].'] ';

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