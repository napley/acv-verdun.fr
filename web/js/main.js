$(document).ready(function() {
    
    $("#menu").tinyNav({
        active: 'selected', // String: Set the "active" class
        header: 'Menu', // String: Specify text for "header" and show header instead of the active item
        indent: '- ', // String: Specify text for indenting sub-items
        label: '' // String: Sets the <label> text for the <select> (if not set, no label will be added)
    });
    
    $('ul.sf-menu').superfish({
            delay:       800,                            // one second delay on mouseout
            animation:   {opacity:'show',height:'show'},  // fade-in and slide-down animation
            speed:       'fast',                          // faster animation speed
            autoArrows:  false                            // disable generation of arrow mark-up
    });

    if ( $( "#course_link" ).length ) {
        addChooseFile('#course_link', 'files', 'files/public', null);
    }
    
    if ($('#infosite_img').is(':checked')) {
        addChooseFile('#infosite_valeur', 'images', null, 'margin-top:-50px;');
    }
    
    $("#infosite_img").change(function() {
        if ($(this).is(':checked')) {
            addChooseFile('#infosite_valeur', 'images', null, 'margin-top:-50px;');
        }
        else {
            $( "#selectFile" ).remove();
        }
    });
    
    function addChooseFile (inputId, type, dir, style) {
        $( "<span id='selectFile' style='"+style+"' class='btn btn-default'><i class='fa fa-file'></i></span>" ).insertAfter( inputId );
        $( "#selectFile" ).click(function (e) {
            window.KCFinder = {
                callBack: function(url) {
                    $(inputId).val(url);
                    window.KCFinder = null;
                }
            };
            window.open('/update/web/kcfinder/browse.php?type='+type+'&dir='+ dir, 'kcfinder_textbox',
                'status=0, toolbar=0, location=0, menubar=0, directories=0, ' +
                'resizable=1, scrollbars=0, width=800, height=600'
            );
        });
    }
    
    $( ".selectFileAuto" ).click(function (e) {
       
    });

    // Tab behaviour.
    $('#myTab a:first').click(function (e) {
        e.preventDefault();
        $(this).tab('show');
    })

    $('#myTab a:last').click(function (e) {
        e.preventDefault();
        $(this).tab('show');
    })
    
    $('#selectall').click(function(event) {
        $('.checkboxCat').each(function() { //loop through each checkbox
                this.checked = true;  //select all checkboxes with class "checkbox1"              
        });
    });
    
    $('#unselectall').click(function(event) {
        $('.checkboxCat').each(function() { //loop through each checkbox
                this.checked = false;  //select all checkboxes with class "checkbox1"              
        });
    });
    
    $( "#add_under_page" ).click(function() {
        var html = $( ".copyTr" ).html();
        $( "<tr>" + html + "</tr>" ).insertBefore(".copyTr");
    });
});	
    
function addPage(element) {
    var val = $( element ).val();
    if (val !== '') {
        $( element ).prev().prev().val(val);
    }
}
    
function selectFileAuto(element) {
    var element = element;
    window.KCFinder = {
        callBack: function(url) {
            $(element).prev().val(url);
            window.KCFinder = null;
        }
    };
    window.open('/update/web/kcfinder/browse.php?type=files&dir=files/public', 'kcfinder_textbox',
        'status=0, toolbar=0, location=0, menubar=0, directories=0, ' +
        'resizable=1, scrollbars=0, width=800, height=600'
    );
}
    
function deleteUnderPage(element) {
    $(element).parent().parent().remove();
}
    
function upUnderPage(element) {
    var row = $(element).parents("tr:first");
    row.insertBefore(row.prev());
}
    
function downUnderPage(element) {
    var row = $(element).parents("tr:first");
    row.insertAfter(row.next().not( ".hidden" ));
}