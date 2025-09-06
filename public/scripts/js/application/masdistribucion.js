Masdist= {};

Masdist.createMessagebox=function(title,content,buttons,options,tagid){
    if(!title) title='';
    if(!content) content='';
    if(!buttons || !$.isPlainObject(buttons)) buttons={};
    if(!options || !$.isPlainObject(options)) options={};
    
    var defaultButtons = {
        close: {
            text:'Cerrar',
            click: function(){
               $(this).dialog('close');
            }
        }
    }

    var btns =  buttons.close ? buttons : defaultButtons;
    var defaultOptions = {
                            autoOpen: false,
                            modal:true,
                            resizable:false,
                            title:title,
                            buttons: btns,
                            closeOnEscape: false,
                            open: function(event, ui) { 
                                $(".ui-dialog-titlebar-close", ui.dialog | ui).hide();
                            }
                        }

    var opts = $.extend({},options,defaultOptions);
      

    var markupContent = '<p>'+ (content ? content : '') +'</p>';
    var $dialogMessage;
    var defaultCont = $('<div></div>');
    var $obdialog = null;


    if(tagid){
        $obdialog = $('#' + tagid);

        if($obdialog  instanceof jQuery)
            $dialogMessage = $obdialog.dialog(opts);            
        else
            $dialogMessage = defaultCont.html(markupContent).dialog(opts);
     }else
        $dialogMessage = defaultCont.html(markupContent).dialog(opts);

    $dialogMessage.dialog('open');
}

$(document).ready(function() {  

	/**********************************MENU PARA DISPOSITIVOS***********************************************/
	var touch 	= $('#touch-menu');
	var menu 	= $('.menu');

	//alert($(window).width());
	
	$(touch).on('click', function(e) {
		e.preventDefault();
		menu.slideToggle();
	});

	$(window).resize(function(){
		var w = $(window).width();
		if(w > 923 && menu.is(':visible')) {
			menu.hide();
		}
	});

	$(".menu > li").click(function(){
		if($(this).find("li > a").is(':visible')){
			$(this).find("li > a").hide();
		}else{
			$(this).find("li > a").show();
			$(this).find("li > a").css({
				//"background-color":"#AAABBA",
				"font-size":"2.5em",
				"font-weight":"normal",
				"color":"#3f3f3f",
				/*"border-left":"3px solid #ffffff",*/
				"padding":"1em",
				"display":"block"
			});
		}
	});
	/*************************************************************************************************************/
});

