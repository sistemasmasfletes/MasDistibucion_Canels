
$(document).ready( function(){
    tinyMCE.init({
        language : "en", // change language here
        mode : "textareas",
        theme : "advanced",
        /*
         *Lista original de plugins
         *plugins : "safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
         */
        plugins : "safari,pagebreak,style,layer,table,advhr,advimage,advlink,emotions,iespell,insertdatetime,preview,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,",
        /*
         *Lista de opciones original del modo
         * theme_advanced_buttons1 : save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect
         * theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
         * theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
         * theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak",
         **/
        theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,formatselect,fontselect,fontsizeselect",
        theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,|,insertdate,inserttime,preview,|,forecolor,backcolor",
        theme_advanced_buttons3 : "sub,sup,charmap,hr,|,fullscreen",
        theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "left",
        //theme_advanced_statusbar_location : "bottom",
        theme_advanced_resizing : false,
        extended_valid_elements : "a[name|href|target|title|onclick],img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name],hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style]",
        //template_external_list_url : "example_template_list.js",
        use_native_selects : true,
        // Example content CSS (should be your site CSS)
        content_css : baseUrl+"/styles/css/tiny_mce.css",
        external_link_list_url : "js/link_list.js"

        //execcommand_callback : "myCustomExecCommandHandler"
    });
});

function myCustomExecCommandHandler(editor_id, elm, command, user_interface, value)
{
    inst = tinyMCE.getInstanceById(editor_id);
    switch (command)
    {
        case "mceAdvLink":
//             inst = tinyMCE.getInstanceById(editor_id);
//             if(inst.selection.getContent().length > 0)
//             {
//                    var bm = inst.selection.getBookmark();
//                    alert(bm);
//                    $('#textSelected').val(bm);
//                    $('#textSelected2').text(inst.selection.getContent());
//
//             }
//              //initLinkDialog('#link');
            $('#tipo').val(1);
//             showLinkDialog(false, editor_id);
//           showLinkDialog(false);
            return true;
    }

    return false; // Pass to next handler in chain
}

function createLink(url, interno, targetLink)
{
//        var textSelected = inst.selection.getContent();
//        var bm;
//
//        if(textSelected.length <= 0)
//        {
//            bm = $('#textSelected').val();
//            textSelected = $('#textSelected2').text(); //mceSetContent
//            inst.focus();
//            inst.selection.moveToBookmark(bm);
//        }
        if(inst.selection.getContent().length > 0)
        {
            var link = '<a _mce_href='+url+' href='+url;
            link += (targetLink == true) ? ' target="_blank">' : ' target="_self">';
    //     link +=textSelected+'</a>';
            link += inst.selection.getContent()+'</a>';
            tinyMCE.execCommand('mceReplaceContent',false, link);
        }
        else
        {
            alert('It has not been possible to establish the link, please try again.');
        }
}

