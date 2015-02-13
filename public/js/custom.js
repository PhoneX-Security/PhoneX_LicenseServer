/**
 * JS utilities
 * author: miroc
 */

$readyFn = function(){
    if (typeof jQuery != 'undefined') {
        // jQuery is loaded => print the version
        //alert(jQuery.fn.jquery);
    }


    console.log( "JS Document ready!" );
    console.log( "JQuery version loaded:" + jQuery.fn.jquery );


    $('.input-group.date').datepicker({

    });




    //
    //$('#users_create_has_access').click(function(){
    //    if($(this).is(':checked')){
    //        $('#users_create_password').removeAttr('disabled');
    //        $('#users_create_password_confirmation').removeAttr('disabled');
    //
    //    }else{
    //        $('#users_create_password').attr('disabled', 'disabled');
    //        $('#users_create_password_confirmation').attr('disabled', 'disabled');
    //    }
    //});
};

$(document).ready(function(){
    $readyFn();
});