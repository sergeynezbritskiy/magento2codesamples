define([
    'jquery',
    'jcf',
    'vue'
], function ($, jcf, Vue) {
    var borrowBtn = $('#product-try-button'),
        borrowUrl = borrowBtn.attr('action'),
        addToCartForm = borrowBtn.parents('#product_addtocart_form');


//function for update count items
    function updateItemsCounter() {
        if ($('.count .max-items').length) {
            var maxItems = parseInt($('.count .max-items').text());
            var currItems = $('.empty-item').length;
            $('.count .counter-number').text(maxItems - currItems);
            return maxItems - currItems;
        }
    }

    borrowBtn.on('click', function () {
        if (borrowUrl !== '#') {
            addToCartForm.attr('action', borrowUrl).submit();
            return false;
        }
    });

    $('.borrow-remove').on('click', function () {
        var el = $(this);
        $(this).closest('.products-grid').addClass('eo-loading');
        $.ajax({
            type: 'GET',
            url: $(this).attr('href'),
            processData: false,
            contentType: false,
            cache: false,
            dataType: 'json',
            success: function (data, xhr) {
                if (xhr === 'success') {
                    if (typeof data['contentHtml'] !== 'undefined') {
                        $(el).closest('li').remove();
                        $('.borrow-products ol.product-items').append(data['contentHtml']);
                        updateItemsCounter();
                        $('.eo-loading').removeClass('eo-loading');
                    }
                } else {
                    alert(data.errors);
                }
            }
        });

        return false;
    });
    return function (config, node) {
        if ((config.customer || config.length) && node.nodeName === 'FORM') {
            if(jcf.getInstance(document.getElementById('borrow_home_country_id'))) {
                jcf.getInstance(document.getElementById('borrow_home_country_id')).destroy();
            }
            var app = new Vue({
                el: '#' + $(node).attr('id'),
                data: {
                    customer: config,
                    country_select: $('#borrow_home_country_id').val()
                },
                mounted: function () {
                    jcf.replace('#borrow_home_country_id');
                }
            });
        }
    }

});