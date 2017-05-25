/*global multitool_setup_params */
jQuery( function( $ ) {

    $( '.button-next' ).on( 'click', function() {
        $('.multitool-setup-content').block({
            message: null,
            overlayCSS: {
                background: '#fff',
                opacity: 0.6
            }
        });
        return true;
    } );

    $( '.multitool-wizard-plugin-extensions' ).on( 'change', '.multitool-wizard-extension-enable input', function() {
        if ( $( this ).is( ':checked' ) ) {
            $( this ).closest( 'li' ).addClass( 'checked' );
        } else {
            $( this ).closest( 'li' ).removeClass( 'checked' );
        }
    } );

    $( '.multitool-wizard-plugin-extensions' ).on( 'click', 'li.multitool-wizard-extension', function() {
        var $enabled = $( this ).find( '.multitool-wizard-extension-enable input' );

        $enabled.prop( 'checked', ! $enabled.prop( 'checked' ) ).change();
    } );

    $( '.multitool-wizard-plugin-extensions' ).on( 'click', 'li.multitool-wizard-extension table, li.multitool-wizard-extension a', function( e ) {
        e.stopPropagation();
    } );
} );
