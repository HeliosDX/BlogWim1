/*$('#click').click(function(event) {
    $('.testpopup').dialog({
        fadeDuration: 100
    });
});*/

/*$(function() {
    $(".toggle-menu").on("click", function(e) {
        e.preventDefault();
        $("#menu_presentation").hide();
    });
});*/

$(function() {
    $(".click").on("click", function(e) {
        e.preventDefault();
        $("#edit_name").html('<img src="http://www.designereyewear.co.uk/img/front/ajax-loader.gif"/>');
        $("#edit_name").load(this.href, function() {
            $(this).dialog("option", "title", $(this).find("h1").text());
            $(this).find("h1").remove();
        });
    });
});

$(function() {
    $(".click_editarticle").on("click", function(e) {
        $("#edit_article").html('<img src="http://www.designereyewear.co.uk/img/front/ajax-loader.gif"/>');
        $("#edit_article").load(this.href);
    });
});
