$(document).ready(function() {
    $('#trucking-cost-form').submit(function(event) {
        event.preventDefault();

        $.ajax({
            url: 'main.php', // Updated to use main.php
            method: 'POST',
            data: $(this).serialize(),
            success: function(data) {
                $('#result').html(data);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                $('#result').html('<p>Error: ' + textStatus + ', ' + errorThrown + '</p>');
            }
        });
    });
});
