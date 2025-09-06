function ResizeGridService(){

    this.resize = function(gridId, parent){
        angular.element(window).on('resize', function(event, ui) {
            var parentWidth=0;
            if(parent)
                parentWidth = parent.width();
            else
                parentWidth = angular.element("#gbox_"+gridId).parent().parent().parent().width();

            var currentWidth = angular.element("#gbox_"+gridId).width();
            var w = parentWidth - 1;
            if (Math.abs(w - currentWidth) > 2)
                angular.element("#"+gridId).setGridWidth(w);
        }).trigger('resize');
    }
    
}