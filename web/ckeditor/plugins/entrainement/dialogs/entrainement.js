jQuery.extend({
getValues: function(url) {
    var result = null;
    var objEntrainement = [];
    $.ajax({
        url: '/update/web/api/entrainements',
        type: 'get',
        dataType: 'json',
        async: false,
        success: function(element) {
            for (var i=0;i<element.length;i++) {
                objEntrainement.push([element[i].title, element[i].id]);
            }
            result = objEntrainement;
        }
    });
   return result;
}
});
var results = $.getValues('.../ckeditor/plugins/PLUGINNAME/ajax.php');

CKEDITOR.dialog.add( 'entrainementDialog', function( editor ) {
    
    return {
        title: 'Choix de l\'Entrainement',
        minWidth: 400,
        minHeight: 200,

        contents: [
            {
                id: 'tab-basic',
                label: 'Choisir un entrainement',
                elements: [
                    {
                        type: 'select',
                        id: 'entrainement',
                        label: 'Entrainement',
                        validate: CKEDITOR.dialog.validate.notEmpty( "Entrainement field cannot be empty." ),
                        items : results,
                        
                        commit: function( element ) {
                            element.setText( this.getValue() );
                        }
                    }
                ]
            }
        ],

        onShow: function() {
            var selection = editor.getSelection();
            var element = selection.getStartElement();

            if ( element )
                element = element.getAscendant( 'entrainement', true );

            if ( !element || element.getName() != 'entrainement' ) {
                element = editor.document.createElement( 'entrainement' );
                this.insertMode = true;
            }
            else
                this.insertMode = false;

            this.element = element;
            if ( this.insertMode )
                this.setupContent( this.element );
        },

        onOk: function() {
            var dialog = this;
            var entrainement = this.element;
            this.commitContent( entrainement );
            if ( this.insertMode )
                editor.insertText('## entrainement '+ entrainement.getText() + ' ##' );
        }
    };
});