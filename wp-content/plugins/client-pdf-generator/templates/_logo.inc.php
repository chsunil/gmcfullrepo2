<?php
/**
 * Shared logo helper — included by QMS PDF templates.
 * Sets $LOGO to the base64 data URI for the GMCSPL logo.
 * Reads once from qms-f11.php to avoid duplicating the huge base64 string.
 */
if ( empty($LOGO) ) {
    $f11 = @file_get_contents( __DIR__ . '/qms-f11.php' );
    if ( $f11 && preg_match( '/src="(data:image[^"]+)"/', $f11, $m ) ) {
        $LOGO = $m[1];
    } else {
        $LOGO = '';
    }
}
