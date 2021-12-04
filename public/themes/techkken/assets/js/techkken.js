
//Scroll navbar 

$(window).on("scroll", function () {
    if ($(window).scrollTop() > 50) {
        $("#header").addClass("active");
    } else {
        $("#header").removeClass("active");
    }
});


$(document).ready(function () {
    var current = location.pathname;
    current = location.pathname.split('/')[1];
    var ishome = true;
    $('#header ul.nav li').each(function () {
        var $this = $(this);
        
        if($this.attr('data-path')!=null){
            if($this.attr('data-path') == current){
                $this.addClass("active");
                ishome = false;
            }else{
                $this.removeClass("active");
            }
        }

    });
    if(ishome){
        $("li[data-path='home']").addClass("active");
    }

});