<?php

if ( !function_exists( 'is_base64' ) ) {
    /**
     * Check if a string is encoded in base 64
     *
     * @param string $value String to be evaluated
     * @return bool
     */
    function is_base64( string $value ) {
        return base64_encode( base64_decode( $value, true) ) === $value;
    }
}