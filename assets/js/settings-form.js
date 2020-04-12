'use strict';
jQuery(function( $ ) {
    var token_field = $( '#r_updater_token' );
    var user_fields = $( '#r_updater_username, #r_updater_password' );

    function show(element) {
        element.closest( 'tr' ).removeAttr( 'style' );
    }

    function hide(element) {
        element.closest( 'tr' ).css( { display: 'none' } );
    }

    function onLoad() {
        $( document ).find( '#r_updater_auth_types' ).trigger( 'change' );
    }

    function handleChange() {
        switch (this.value) {
            case 'basic': show(user_fields); hide(token_field); break;
            case 'token': show(token_field); hide(user_fields); break;
        }
    }

    // Launch JQuery events
    $( document ).ready( onLoad );
    $( '#r_updater_auth_types' ).on( 'change', handleChange );
});