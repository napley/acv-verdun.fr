CKEDITOR.plugins.add( 'entrainement', {
    icons: 'entrainement',
    init: function( editor ) {

        editor.addCommand( 'entrainement', new CKEDITOR.dialogCommand( 'entrainementDialog' ) );

        editor.ui.addButton( 'Entrainement', {
            label: 'Insert Entrainement',
            command: 'entrainement',
            toolbar: 'insert'
        });

        CKEDITOR.dialog.add( 'entrainementDialog', this.path + 'dialogs/entrainement.js' );
    }
});