/* global multitool_admin */

/**
 * Multitool Admin JS
 */
jQuery( function ( $ ) {

    // Demo store notice
    $( 'input#woocommerce_demo_store' ).change(function() {
        if ( $( this ).is( ':checked' ) ) {
            $( '#woocommerce_demo_store_notice' ).closest( 'tr' ).show();
        } else {
            $( '#woocommerce_demo_store_notice' ).closest( 'tr' ).hide();
        }
    }).change();
    
    // Administration Accounts Cap
    $( 'input#adminaccountscap_activate' ).change(function() {
        if ( $( this ).is( ':checked' ) ) {
            $( '#adminaccountscap_alerttype' ).closest( 'tr' ).show();
        } else {
            $( '#adminaccountscap_alerttype' ).closest( 'tr' ).hide();
        }
        if ( $( this ).is( ':checked' ) ) {
            $( '#adminaccountscap_limit' ).closest( 'tr' ).show();
        } else {
            $( '#adminaccountscap_limit' ).closest( 'tr' ).hide();
        }
    }).change();    

});