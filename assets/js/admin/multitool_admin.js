/* global multitool_admin */

/**
 * Multitool Configuration Tools show/hide Functionality
 */
jQuery( function ( $ ) {

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

    // Maintenace Mode
    $( 'input#multitool_maintenancemode_activate' ).change(function() {
        if ( $( this ).is( ':checked' ) ) {
            $( '#multitool_maintenancemode_timelimit' ).closest( 'tr' ).show();
        } else {
            $( '#multitool_maintenancemode_timelimit' ).closest( 'tr' ).hide();
        }
    }).change();    
    
});