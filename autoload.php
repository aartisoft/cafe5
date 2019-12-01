<?php


spl_autoload_register( 'cafe5_autoloader' );

function cafe5_autoloader( $class_name ) {

	/**
     * If the class being requested does not start with our prefix,
     * we know it's not one in our project
     */
    if ( 0 !== strpos( $class_name, 'cafe5_' ) ) {
        return;
    }

    $file_name = str_replace(
        array( 'cafe5_', '_' ),      // Prefix | Underscores 
        array( '', '-' ),         // Remove | Replace with hyphens
        strtolower( $class_name ) // lowercase
    );

    // Compile our path from the current location
    $file = dirname( __FILE__ ) . '/class/'. $file_name .'.php';

    // If a file is found
    if ( file_exists( $file ) ) {
        // Then load it up!

        require( $file );

    }
}

?>