jQuery(document).ready(function($) {
    $('.quote-filter').on('change', function() {
        var filter = {
            'author': $('#author-filter').val(),
            'category': $('#category-filter').val(),
            'tag': $('#tag-filter').val()
        };

        $.ajax({
            url: quotepress_ajax_params.ajax_url,
            type: 'POST',
            data: {
                action: 'filter_quotes',
                nonce: quotepress_ajax_params.nonce,
                filter: filter
            },
            success: function(response) {
                $('#quotes-list').html(response);
            }
        });
    });
});
