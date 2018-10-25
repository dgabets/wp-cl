(function() {
  tinymce.PluginManager.add('clutch_upsell_button', function( editor, url ) {
    editor.addButton( 'clutch_upsell_button', {
      text: 'Upsell',
      icon: false,
      onclick: function() {
        editor.insertContent( '<br/>' + tinyMCE_object.UPSELL_VALUE + '<br/>');
      },
    });
  });
})();