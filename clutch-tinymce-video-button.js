(function() {
  tinymce.PluginManager.add('clutch_video_button', function( editor, url ) {
    editor.addButton( 'clutch_video_button', {
      text: tinyMCE_object.button_name,
      icon: false,
      onclick: function() {
        editor.windowManager.open( {
          title: tinyMCE_object.button_title,
          body: [
            {
              type: 'textbox',
              name: 'video',
              label: 'video',
              value: '',
              classes: 'clutch-input-video',
            },
            {
              type: 'textbox',
              name: 'poster',
              label: 'poster',
              value: '',
              classes: 'clutch-input-poster',
            },
            {
              type: 'button',
              name: 'image',
              classes: 'sidetag-media-button',
              text: 'Insert video',
              onclick: function( e ) {

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
                    title: 'Select or Upload Video',
                    library: {type: 'video'},
                    button: {
                      text: 'Use this video'
                    },
                    multiple: false  // Set to true to allow multiple files to be selected
                  });

                  // When an image is selected in the media frame...
                  frame.on( 'select', function() {
                    var attachment = frame.state().get('selection').first().toJSON();
                    $( '.mce-clutch-input-video' ).val( attachment.url );
                  });
                  frame.open();
                });
                return false;
              }
            },
            {
              type: 'button',
              name: 'image',
              classes: 'sidetag-media-button',
              text: 'Insert poster',
              onclick: function( e ) {

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
                      text: 'Use this image'
                    },
                    multiple: false  // Set to true to allow multiple files to be selected
                  });

                  // When an image is selected in the media frame...
                  frame.on( 'select', function() {
                    var attachment = frame.state().get('selection').first().toJSON();
                    $( '.mce-clutch-input-poster' ).val( attachment.url );
                  });
                  frame.open();
                });
                return false;
              }
            }
          ],
          onsubmit: function( e ) {

            console.error(e.data);

            editor.insertContent( '[video src="' + e.data.video + '" poster="' + e.data.poster + '"]');
          }
        });
      },
    });
  });

})();