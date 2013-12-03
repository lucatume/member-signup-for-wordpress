/*global jQuery:false*/
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