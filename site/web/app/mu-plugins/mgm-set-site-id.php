<?php
/**
* Plugin Name: Multisite Global Media Site ID
* Plugin URI:  https://github.com/bueltge/multisite-global-media/
* Description: Set my Multisite Global Media site in the network.
* Version:     1.0.0
* Network:     true
*/

add_filter( 'global_media.site_id', function() {
   return 16;
} );
