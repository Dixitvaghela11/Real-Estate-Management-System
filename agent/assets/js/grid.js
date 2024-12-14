$(document).ready(function() {
    // Sorting functionality
    $('.order_by_date, .order_by_price').on('click', function() {
        var orderBy = $(this).data('orderby');
        var order = $(this).data('order');

        // Toggle the order
        if ($(this).data('order') === 'ASC') {
            $(this).data('order', 'DESC');
            $(this).find('i').removeClass('fa-sort-amount-asc').addClass('fa-sort-amount-desc');
        } else {
            $(this).data('order', 'ASC');
            $(this).find('i').removeClass('fa-sort-amount-desc').addClass('fa-sort-amount-asc');
        }

        // Update the UI to reflect the sorting
        $('.sort-by-list li').removeClass('active');
        $(this).parent().addClass('active');

        // AJAX request to fetch sorted data
        $.ajax({
            url: 'sort_properties.php',
            type: 'GET',
            data: { orderby: orderBy, order: order },
            success: function(response) {
                $('#list-type').html(response);
            }
        });
    });

    // Layout switcher functionality
    $('.layout-list, .layout-grid').on('click', function() {
        var layout = $(this).hasClass('layout-list') ? 'list' : 'grid';

        // Update the UI to reflect the layout change
        $('.layout-list, .layout-grid').removeClass('active');
        $(this).addClass('active');

        if (layout === 'list') {
            $('#list-type').removeClass('proerty-th').addClass('proerty-list');
        } else {
            $('#list-type').removeClass('proerty-list').addClass('proerty-th');
        }
    });
});