define([
    'jquery',
    'vue',
    'vue-resource',
    'Magento_Customer/js/customer-data',
    'Magento_Ui/js/modal/confirm',
    'mage/translate'
], function ($, Vue, vueresource, customerData, confirm) {
    customerData.reload(['borrow_home', 'cart']);
    customerData.get('borrow_home').subscribe(function (data) {
        var event = $.Event('borrow_cart:update');
        event.card = data;
        $('[data-block="minicart"]').trigger(event);
    });
    customerData.get('cart').subscribe(function (data) {
        var event = $.Event('cart:update');
        event.card = data;
        $('[data-block="minicart"]').trigger(event);
    });

    //function for show/hide unneeded templates
    function style(el) {
        if ($(el).closest('.block-minicart').find('.empty-block').length) {
            $(el).closest('.block-minicart').addClass('bordered');
        } else {
            $(el).closest('.block-minicart').removeClass('bordered');
        }
    }

    return function (config, node) {
        $(node).addClass('hidden');
        $('[data-block="minicart"]').on('borrow_cart:update', function (e) {
            app.borrow_cart = e.card;
        });
        $('[data-block="minicart"]').on('cart:update', function (e) {
            app.cart = e.card;
        });
        Vue.use(vueresource);
        var app = new Vue({
            el: node,
            data: {
                borrow_cart: {},
                cart: {}
            },
            computed: {},
            watch: {
                borrow_cart: function (val, oldVal) {
                    this.updateCount(val.summary_count, app.cart.summary_count);
                },
                cart: function (val, oldVal) {
                    this.updateCount(app.borrow_cart.summary_count, val.summary_count);
                }
            },
            methods: {
                updateCount: function (val, cart_val) {
                    if (val + cart_val === 0 || isNaN(val + cart_val)) {
                        $('.minicart-wrapper .action.showcart .counter.qty').addClass('empty');
                    } else {
                        $('.minicart-wrapper .action.showcart .counter.qty').removeClass('empty').text(val + cart_val);
                    }
                },
                deleteItem: function (url) {
                    confirm({
                        content: $.mage.__('Are you sure you would like to remove this item from the shopping cart?'),
                        actions: {
                            confirm: function () {
                                $.ajax({
                                    method: 'POST',
                                    url: url
                                });
                            }
                        }
                    });
                }
            },
            created: function () {
                var el = $(node);
                el.removeClass('hidden');
            },
            mounted: function () {
                var el = $(node);
            }
        });
    }
});