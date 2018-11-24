(function() {
  tinymce.PluginManager.add('clutch_img_upsell_button', function( editor, url ) {
    editor.addButton( 'clutch_img_upsell_button', {
      text: 'Upsell image',
      icon: false,
      onclick: function() {
        jQuery(function($){
          // Set all variables to be used in scope
          var frame;

          // ADD IMAGE LINK
          event.preventDefault();

          // If the media frame already exists, reopen it.
          if ( frame ) {
            frame.open();
            return;
          }

          // Create a new media frame
          frame = wp.media({
            title: 'Select or Upload Image',
            library: {type: 'image'},
            button: {
              text: 'Use this Image'
            },
            multiple: false  // Set to true to allow multiple files to be selected
          });

          // When an image is selected in the media frame...
          frame.on( 'select', function() {
            var attachment = frame.state().get('selection').first().toJSON();
            editor.insertContent( '<br/><img src="' + attachment.url + '" alt="' + tinyMCE_object.UPSELL_TITLE + '" /><br/>');

            $( '.mce-clutch-input-video' ).val( attachment.url );
          });
          frame.open();
        });
        return false;
      },
    });
  });
})();