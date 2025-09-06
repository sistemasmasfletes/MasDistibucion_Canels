<div class="contiene-bread">
    <ol class="breadcrumb">
    <li class="active">Catálogos</li>
    <li class="active actualpg ">Categorías</li>
</ol>
</div>
<div class="container">
    <div class="row" >
        <!--<div class="span12 blockGray">-->
            <div class="blockInner">
                <h1>Categorias</h1>
                <?php
                echo '<a class="btn btn-default sin-padding" href="' . $view->url(array('action' => 'add')) . '" ><span class="pers-btn icono-agregar-categoria tam-normal"></span></a>';
                echo '<div class="clear" style="height:15px;" ></div>';
                echo '<label for="kwd_search">Buscar:</label> <input type="text" id="kwd_search" value=""/>';
                $view->sc->generatetable();
                ?>
               	<ul class="pagination pagination-lg pager" id="myPager"></ul>
            </div>
        <!--</div>-->
    </div>
</div>

<script type="text/javascript">

	$.fn.pageMe = function(opts){
	    var $this = this,
	        defaults = {
	            perPage: 8,
	            showPrevNext: false,
	            hidePageNumbers: false
	        },
	        settings = $.extend(defaults, opts);
	    
	    var listElement = $this;
	    var perPage = settings.perPage; 
	    var children = listElement.children();
	    var pager = $('.pager');
	    
	    if (typeof settings.childSelector!="undefined") {
	        children = listElement.find(settings.childSelector);
	    }
	    
	    if (typeof settings.pagerSelector!="undefined") {
	        pager = $(settings.pagerSelector);
	    }
	    
	    var numItems = children.size();
	    var numPages = Math.ceil(numItems/perPage);
	
	    pager.data("curr",0);
	    
	    if (settings.showPrevNext){
	        $('<li><a href="#" class="prev_link"> < </a></li>').appendTo(pager);
	    }
	    
	    var curr = 0;
	    while(numPages > curr && (settings.hidePageNumbers==false)){
	        $('<li><a href="#" class="page_link">'+(curr+1)+'</a></li>').appendTo(pager);
	        curr++;
	    }
	    
	    if (settings.showPrevNext){
	        $('<li><a href="#" class="next_link"> > </a></li>').appendTo(pager);
	    }
	    
	    pager.find('.page_link:first').addClass('active');
	    pager.find('.prev_link').hide();
	    if (numPages<=1) {
	        pager.find('.next_link').hide();
	    }
	  	pager.children().eq(1).addClass("active");
	    
	    children.hide();
	    children.slice(0, perPage).show();
	    
	    pager.find('li .page_link').click(function(){
	        var clickedPage = $(this).html().valueOf()-1;
	        goTo(clickedPage,perPage);
	        return false;
	    });
	    pager.find('li .prev_link').click(function(){
	        previous();
	        return false;
	    });
	    pager.find('li .next_link').click(function(){
	        next();
	        return false;
	    });
	    
	    function previous(){
	        var goToPage = parseInt(pager.data("curr")) - 1;
	        goTo(goToPage);
	    }
	     
	    function next(){
	        goToPage = parseInt(pager.data("curr")) + 1;
	        goTo(goToPage);
	    }
	    
	    function goTo(page){
	        var startAt = page * perPage,
	            endOn = startAt + perPage;
	        
	        children.css('display','none').slice(startAt, endOn).show();
	        
	        if (page>=1) {
	            pager.find('.prev_link').show();
	        }
	        else {
	            pager.find('.prev_link').hide();
	        }
	        
	        if (page<(numPages-1)) {
	            pager.find('.next_link').show();
	        }
	        else {
	            pager.find('.next_link').hide();
	        }
	        
	        pager.data("curr",page);
	      	pager.children().removeClass("active");
	        pager.children().eq(page+1).addClass("active");
	    
	    }
	};

    $(document).ready(function(){
        
  	  $('#myTable').pageMe({pagerSelector:'#myPager',showPrevNext:true,hidePageNumbers:false,perPage:8});
  	  
		$("#kwd_search").keyup(function(){
			if( $(this).val() != ""){
				$("#myTable>tr").hide();
				$("#myTable td:contains-ci('" + $(this).val() + "')").parent("tr").show();
			}else{
				$("#myTable>tr").show();
			}
		});

	});
	// jQuery expression for case-insensitive filter
	$.extend($.expr[":"], 
	{
	    "contains-ci": function(elem, i, match, array) 
		{
			return (elem.textContent || elem.innerText || $(elem).text() || "").toLowerCase().indexOf((match[3] || "").toLowerCase()) >= 0;
		}
	});    
</script>