/**
 * Created with JetBrains PhpStorm.
 * User: Helios
 * Date: 16/10/13
 * Time: 15:08
 * To change this template use File | Settings | File Templates.
 */

$('#menu_identification').click(function(event){
    event.stopPropagation();
});

$(document).ready(function() {
    $('.selectpicker').selectpicker();
});

$(function() {
    $(".changement_email").hide();
    $(".changement_password").hide();
    $(".options_articles").hide();
    $(".options_chat").hide();
    $(".statut_compte").hide();
    $("#link_show_changement_email").click(function(e) {
        e.preventDefault();
        $(".changement_email").slideToggle(400, function() {});
    });
    $("#link_show_changement_password").click(function(e) {
        e.preventDefault();
        $(".changement_password").slideToggle(400, function() {});
    });
    $("#link_show_options_articles").click(function(e) {
        e.preventDefault();
        $(".options_articles").slideToggle(400, function() {});
    });
});

//set opacity to 0.4 for all the images
//opacity = 1 - completely opaque
//opacity = 0 - invisible

// when hover over the selected image change the opacity to 1
$('.avatar').hover(
function(){
    $(this).find('button').stop().fadeTo('fast', 1);
    },
function(){
    $(this).find('button').stop().fadeTo('fast', 0.3);
    });

$(function() {
    $(".avatar_edit_image").draggable({
        axis: "y",
        drag: function(event, ui) {
            var pos = ui.position;
            var $this = $(this);

            if((pos.top * -1) >= ($this.height() - $this.parent().height()))
            {
                event.stopPropagation();
            }

            if(pos.top > 0)
            {
                event.stopPropagation();
            }
        },
        //containment: 'parent',

        // Find original position of dragged image.
        start: function(event, ui) {

            // Show start dragged position of image.
            var Startpos = $(this).position();
            $("#initial_position").text("START: \nLeft: "+ Startpos.left + "\nTop: " + Startpos.top);
        },

        // Find position where image is dropped.
        stop: function(event, ui) {

            // Show dropped position.
            var Stoppos = $(this).position();
            $("#final_position").text("STOP: \nLeft: "+ Stoppos.left + "\nTop: " + Stoppos.top);
            $("#hauteur").text($(".avatar_edit_image").height());
            $("#largeur").text($(".avatar_edit_image").width());
        }
    });
});

$(function() {
    $(".button_valid_editavatar").click(function(){
        var avatar_id = $("#avatar_id").val();
        var blog = $("#blogusername").val();
        var Stoppos = $(".avatar_edit_image").position();
        $(".avatar_image_loader").show();
        $(".avatar_image").css("opacity", 0.3)
        $.ajax({
            type: "GET",
            url: Routing.generate('heliosblog_ajustavatar', { avatar: avatar_id, positionY: Stoppos.top, blog: blog }),
            cache: false,
            success: function(){
                $(".avatar_image_loader").hide();
                $('.avatar_image').css({
                    opacity : 1,
                    'margin-top' : Stoppos.top+"px"
                });
            },
            error: function(xhr,err){
                $(".avatar_image_loader").hide();
                $(".avatar_image").css("opacity", 1);
                alert("erreur");
            }
        });
        return false;
    });
});

/*
    Modifier le nom et la présentation de la page d'accueil
 */
$(function() {
    $("#form_editname").submit(function(){
        var postData = $(this).serializeArray();
        $(".presentation_loader").show();
        $.ajax({
            type: "POST",
            url: $("#form_editname").attr("action"),
            data: postData,
            cache: false,
            success: function(data){
                $(".presentation_name").html(data.name);
                $(".presentation_text").html(data.description);
                $(".presentation_loader").hide();
            },
            error: function(xhr,err){
                $(".contenu_article").html(xhr.responseText);
                alert(xhr.responseText);
            }
        });
        return false;
    });
});

/*$(function() {
//listen for the form beeing submitted
    $("#form_editname").submit(function(){
        //get the url for the form
        var url=$("#form_editname").attr("action");
        var blog = $("#blogusername").val();
        //start send the post request
        $.post(url,{
            form:$("#name_id").val(),
            formDescription:"attributes"
        },function(data){
            if(data.responseCode==200 ){
                $(".presentation_name").html(data.name);
            }
            else if(data.responseCode==400){//bad request
                alert("error");
            }
            else{
                alert("An unexpeded error occured.");
            }
        });//It is silly. But you should not write 'json' or any thing as the fourth parameter. It should be undefined. I'll explain it futher down

        //we dont what the browser to submit the form
        return false;
    });
});*/

/*$(function() {
    $("#form_uploadavatar").submit(function(){
        $(".avatar_image_loader").show();
        $(".avatar_image").css("opacity", 0.3)
        $.ajax({
            type: "POST",
            url: $("#form_uploadavatar").attr("action"),
            cache: false,
            success: function(data){
                $(".avatar_image_loader").hide();
            },
            error: function(xhr,err){
                $(".avatar_image_loader").hide();
                $(".contenu_article").html(xhr.responseText);
            }
        });
        return false;
    });
});*/
