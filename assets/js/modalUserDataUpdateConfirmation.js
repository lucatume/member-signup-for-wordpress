/*! Member Signup - v0.1.0
 * https://github.com/lucatume/member-signup-for-wordpress.git
 * Copyright (c) 2013; * Licensed GPLv2+ */
/*global document:false*/
jQuery(document).ready(function($) {
  $( "#membersignupDataUpdateConfirmationDialog" ).dialog({
      modal: true,
      buttons: {
        Chiudi: function() {
          $( this ).dialog( "close" );
        }
      }
    });
});