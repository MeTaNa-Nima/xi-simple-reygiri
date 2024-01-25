jQuery(document).ready(function($) {
    $('#simple_reygiri_form').on('submit', function(e) {
        e.preventDefault(); // Prevent the default form submission

        // Prepare data for the AJAX request
        var data = {
            'action': 'simple_reygiri_ajax_handler',
            'search': $('#simple_reygiri_form input[name="search"]').val(),
            // Include nonce here if using
            // 'nonce': simple_reygiri_ajax_obj.nonce
        };

        // Execute the AJAX request
        $.post(simple_reygiri_ajax_obj.ajaxurl, data, function(response) {
            // Clear previous results and insert new results
            $('#results_container').empty(); // Clear any previous results
            $('#results_container').html(response); // Insert the new results
        }).fail(function() {
            console.error("Error processing request");
            // Optionally, update the UI to inform the user
        });
    });
});
