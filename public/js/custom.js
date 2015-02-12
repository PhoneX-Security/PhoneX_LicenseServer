
// TODO add on load


/* users.create */
$('#users_create_has_access').click(function(){
    if($(this).is(':checked')){
        $('#users_create_password').removeAttr('disabled');
        $('#users_create_password_confirmation').removeAttr('disabled');

    }else{
        $('#users_create_password').attr('disabled', 'disabled');
        $('#users_create_password_confirmation').attr('disabled', 'disabled');
    }
});





$('body').click(function(){
    alert('!');
});
