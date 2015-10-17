$(function() {
    App.globalInit();

    $(document).ajaxError(function( event, request, settings ) {
        if(request.responseJSON.hasOwnProperty('message'))
        {
            Notifier.error(request.responseJSON.message);
        }
    });
});
