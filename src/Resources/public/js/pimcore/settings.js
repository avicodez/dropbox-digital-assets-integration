// configuration 
$('#settings_submit_btn').on('click', function() {
    let validForm = true;
    $('.import-plugin-flash').hide();

    $("#twohats_dropbox_settings_form").find(':input', ':password').each(function() {

        if(!this.value) {
            validForm = false;
            $(this).addClass('invalid-input');
        } else {
            $(this).removeClass('invalid-input');
        }
    });

    if (validForm) {
        var form = $("#twohats_dropbox_settings_form");
        var actionUrl = form.attr('action');

        $.ajax({
            type: "POST",
            url: actionUrl,
            data: form.serialize(), 
            success: function(data)
            {
                $("#import-plugin-success-flash").html('');
                $("#import-plugin-success-flash").append("<b> " + data.message + "</b>");
                $("#import-plugin-success-flash").show().delay(5000).fadeOut();
            },
            error: function(data)
            {
                $("#import-plugin-error-flash").html('');
                $("#import-plugin-error-flash").append("<b> " + data.responseJSON.message + "</b>");
                $("#import-plugin-error-flash").show().delay(5000).fadeOut();
            }
        });
    }
    
})

// sync
$('#plugin-sync-btn').on('click', function() {
    let isConfigured = true;
    $('.import-plugin-flash').hide();
    $(this).addClass('plugin-invalid-a');
    $('#plugin-sync-loader').show();

    $("#twohats_dropbox_settings_form").find(':input', ':password').each(function() {

        if(!this.value) {
            isConfigured = false;
        }
    });

    if (!isConfigured) {
        $("#import-plugin-error-flash").html('');
        $("#import-plugin-error-flash").append("<b> Please add the configuration details and submit before sync</b>");
        $("#import-plugin-error-flash").show().delay(5000).fadeOut();

        $('#plugin-sync-loader').hide();
        $('#plugin-sync-btn').removeClass('plugin-invalid-a');
        return false;
    }
    
    $.ajax({
        url: '/admin/dropbox_digital_assets_integration/sync/submit',
        success: function(data)
            {
                $('#plugin-sync-loader').hide();
                $("#import-plugin-success-flash").html('');
                $("#import-plugin-success-flash").append("<b> " + data.message + "</b>");
                $("#import-plugin-success-flash").show().delay(5000).fadeOut();
                $('#plugin-sync-btn').removeClass('plugin-invalid-a');
            },
        error: function(data)
            {
                $('#plugin-sync-loader').hide();
                $("#import-plugin-error-flash").html('');
                $("#import-plugin-error-flash").append("<b> " + data.responseJSON.message + "</b>");
                $("#import-plugin-error-flash").show().delay(5000).fadeOut();
                $('#plugin-sync-btn').removeClass('plugin-invalid-a');
            }
    });

});

