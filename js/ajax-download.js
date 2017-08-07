jQuery(document).ready(function ($) {
    postIds = $('#post_ids');

    var ajaxDownload = function(type) {
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'export',
                post_ids: postIds.val().toString(),
                export_type: type.toString()
                //security: RMOE.security
            },
            success: function(response) {
                alert("response" + type + ' ' + postIds.val().toString());
            },
            error: function(error) {
                alert("error");
            }
        });
    }

    $('#export_csv').on("click", function(event) {
        ajaxDownload('csv');
    });
    $('#export_excel').on("click", function(event) {
        ajaxDownload('excel');
    });
});
