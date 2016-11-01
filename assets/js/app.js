var App = {

    globalInit: function()
    {
        App.init.ajaxForm();
    },

    init : {

        ajaxForm : function()
        {
            $('form[data-ajax-form="true"]').submit(function(e)
            {
                e.preventDefault();

                $.ajax({
                    data: $(this).serialize(),
                    method: $(this).attr('method'),
                    url:  $(this).attr("action")
                })
                .done(function(response)
                {
                    $('.error').removeClass('error');
                    Notifier.success(response.msg);

                    if(response.hasOwnProperty('location'))
                    {
                        window.location.href = response.location;
                    }

                    var event = jQuery.Event('ajaxForm.done');
                    event.datas = response.data;
                    $(document).trigger(event);
                })
                .fail(function(response){
                    switch(response.status)
                    {
                        case 500:
                            App.systemError(response);
                            break;
                        case 400:
                        default:
                            App.applicationError(response);
                    }
                })
                ;
            })
        }
    },

    systemError : function(response)
    {
        Notifier.error('Une erreur s\'est produite');
    },

    applicationError : function(response)
    {
        $('.error').removeClass('error');
        var error = '<ul>';
        error += App.getError(response.responseJSON.data);
        error += '</ul>';
        Notifier.error(response.responseJSON.msg+"\n"+stripslashes(error));
    },

    getError: function(data)
    {
        var error = '';
        for(errorIndex in data)
        {
            if(!data[errorIndex].hasOwnProperty('label'))
            {
                error += App.getError(data[errorIndex]);
            }
            else
            {
                error += '<li>';
                error += data[errorIndex]['label']+' : ';
                $('#'+errorIndex)
                    .parent().addClass('error')
                    .find('label:not(.lbl)').addClass('control-label')
                    ;

                error += data[errorIndex]['error'];
                error += '</li>';
            }
        }

        return error;
    }
}
