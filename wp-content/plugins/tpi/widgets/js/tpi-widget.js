jQuery(document).ready(function() { /// Wait till page is loaded


        var body               =   jQuery(this),
        single_product         =   body.find( '.single-product' ),
        woocommerce_cart       =   body.find( '.woocommerce-cart' ),
        catalog                =   body.find('.post-type-archive-product');


    if(jQuery(single_product).length) {

        //To be used later to separate the first and last price
        var price_seperator = '<span class="price-Seperator"> – </span>';

        //Only process logic if on Single Product page

        var variation_price = [];
        var prices = jQuery('div.entry-summary p.price').children();

        //Get the product type
        var product_type = null;
        if (jQuery('div.product').hasClass('product-type-variable')) {
            product_type = 'variable';
        } else if (jQuery('div.product').hasClass('product-type-simple')) {
            product_type = 'simple';
        }

        // Get the product id
        var product_id = jQuery('form.variations_form').attr('data-product_id');
        var dice_flag = 'single-product';

        security = woocommerce_admin_meta_boxes_variations.save_variations_nonce;

        jQuery.ajax({
            url: tpiAjax.ajax_url,
            type: 'POST',//changing data on the server
            data: {
                action: 'dice_get_sale_price',
                security: security, //get through $_POST['security']
                dice_product_id: product_id, //get through $_POST['dice_product_id']
                dice_product_type: product_type,//get through $_POST['dice_product_type']
                dice_flag: dice_flag,
            },
            success: function (response) {

                if (response != "0") {

                    //Check if product_id is in dice_thrown list
                    if (response.sale_price_applied && jQuery.inArray(product_id, response.dice_thrown) !== -1) {

                        jQuery('.discount-title').css("visibility", "visible");
                        jQuery('#discount-block').html(response.percentage.spelled).css("visibility", "visible");
                        jQuery('.tpi-clear-discount').css("visibility", "visible");

                        //Get the product variation prices
                        var amounts = [];
                        jQuery.each(prices, function (jindex, price) {
                            if (jQuery(price).hasClass('woocommerce-Price-amount')) {
                                amounts.push(jQuery(price).text().replace('$', ''));
                            }
                        });

                        //Cross out the original price
                        var deleted_price = '<del>';
                        deleted_price +=
                            '<span class="woocommerce-Price-amount amount dice-thrown">' +
                            '<span class="woocommerce-Price-currencySymbol">$</span>' +
                            amounts[0] +
                            '</span>' +
                            price_seperator +
                            '<span class="woocommerce-Price-amount amount dice-thrown">' +
                            '<span class="woocommerce-Price-currencySymbol">$</span>' +
                            amounts[1] +
                            '</span>';
                        deleted_price += '</del>';

                        jQuery('div.entry-summary p.price').html(deleted_price);

                        var sale_prices = response.sale_prices.variation;
                        var sale_price_min = response.sale_prices.terminus.min;
                        var sale_price_max = response.sale_prices.terminus.max;
                        var sale_price_count = sale_prices.length;

                        if (sale_price_count > 1) {
                            //  Add new prices
                            var woocommerce_variation_sale_price =
                                '<ins>' +
                                '<span class=" woocommerce-Price-amount amount dice-thrown">' +
                                '<span class="woocommerce-Price-currencySymbol">$</span>' +
                                sale_price_min +
                                '</span>' +
                                price_seperator +
                                '<span class=" woocommerce-Price-amount amount dice-thrown">' +
                                '<span class="woocommerce-Price-currencySymbol">$</span>' +
                                sale_price_max +
                                '</span>' +
                                '</ins>';

                            jQuery('div.entry-summary p.price').append(woocommerce_variation_sale_price);

                        } else if (sale_price_count == 1) {

                            var woocommerce_single_sale_price =
                                '<ins>' +
                                '<span class=" woocommerce-Price-amount amount dice-thrown">' +
                                '<span class="woocommerce-Price-currencySymbol">$</span>' +
                                sale_price_min +
                                '</span>' +
                                '</ins>';

                            var single_Price = jQuery('div[itemprop="offers"] > p.price > .single-Price.amount');
                            if (single_Price.length) {
                                single_Price.html(woocommerce_single_sale_price);
                            }

                        }
                    }
                } // response  dice_variation_changed  dice_single_add_to_cart


                //if dice_thrown
                // use variation_changed flag instead tjsherlock 2017May06
                if (response.dice_variation_changed === 'true') {


                    //add styling for original and discounted prices

                    var sale_price_block =
                        '<ins>' +
                        '<span class="woocommerce-Price-amount amount">' +
                        '<span class="woocommerce-Price-currencySymbol">$</span>' +
                        response.discounted_variation_price_selected +
                        '</span>' +
                        '</ins>';

                    jQuery('div.woocommerce-variation.single_variation div.woocommerce-variation-price span.price span.woocommerce-Price-amount.amount').wrap("<del></del>");
                    jQuery('div.woocommerce-variation.single_variation div.woocommerce-variation-price span.price').append(sale_price_block);


                }

            },
            error: function (errorThrown) {
                console.log(errorThrown);
                console.log(tpiAjax.ajax_url);
                alert('Error: single-product class');
            }
        });

    }



    if(jQuery(woocommerce_cart).length) {

        security = woocommerce_admin_meta_boxes_variations.save_variations_nonce;

        jQuery.ajax({
            url: tpiAjax.ajax_url,
            type: 'POST',//changing data on the server
            data: {
                action: 'dice_get_coupon',
                security: security, //get through $_POST['security']
                dice_product_id: product_id, //get through $_POST['dice_product_id']
                dice_product_type: product_type,//get through $_POST['dice_product_type']
                dice_flag: dice_flag,
            },
            success: function (response) {

                if (response != "0") {

                            jQuery('.discount-title').css("visibility", "visible");
                            jQuery('#discount-block').html(response.percentage.spelled).css("visibility", "visible");
                            jQuery('.tpi-clear-discount').css("visibility", "visible");

                    if(response.code){

                    var coupon = '<tr class="cart-discount coupon-' + response.coupon_code + '">';
                    coupon += '<th>Coupon:\n ' + response.coupon_code + '</th>';
                    coupon += '<td>-<span class="woocommerce-Price-amount amount reduced-Price"><span class="woocommerce-Price-currencySymbol">$</span>' + response.discount + '</span> <a href="http://transcentplugin.dev:8888/checkout/?remove_coupon=' + response.coupon_code + '" class="woocommerce-remove-coupon" data-coupon="f' + response.coupon_code + '">[Remove]</a></td>';
                    coupon += '</tr>';
                    jQuery('tr.cart-subtotal').after(coupon);

                    var tax = '<th>Tax</th>';
                    tax += '<td><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">$</span>' + response.tax_total + '</span></td>';
                    jQuery('tr.tax-total').html(tax);

                    var total = '<th>Total</th>';
                    total += '<td><strong><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">$</span>' + response.order_total + '</span></strong> </td>';
                    jQuery('tr.order-total').html(total);


                    var cart_totals = '<div class="cart_totals calculated_shipping">';
                    cart_totals += '<h2>Cart Totals</h2>';
                    cart_totals += '<table cellspacing="0" class="shop_table shop_table_responsive">';
                    cart_totals += '<tbody><tr class="cart-subtotal">';
                    cart_totals += '<th>Subtotal</th>';
                    cart_totals += '<td data-title="Subtotal"><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">$</span>' + response.subtotal + '</span></td> </tr>';
                    cart_totals += '<tr class="cart-discount coupon-' + response.coupon_code + '">';
                    cart_totals += '<th>Coupon:\n' + response.coupon_code + '</th>';
                    cart_totals += '<td data-title="Coupon: ' + response.coupon_code + '"><span class="negative-sign">-</span><span class="woocommerce-Price-amount amount reduced-Price"><span class="woocommerce-Price-currencySymbol">$</span>' + response.discount + '</span> <a href="http://transcentplugin.dev:8888/cart/?remove_coupon=' + response.coupon_code + ' " class="woocommerce-remove-coupon" data-coupon="' + response.coupon_code + '">[Remove]</a></td>';
                    cart_totals += '</tr>';
                    cart_totals += '<tr class="tax-total">';
                    cart_totals += '<th>Tax</th>';
                    cart_totals += '<td data-title="Tax"><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">$</span>' + response.tax_total + '</span></td>';
                    cart_totals += '</tr>';
                    cart_totals += '<th>Total</th>';
                    cart_totals += '<td data-title="Total"><strong><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">$</span>' + response.order_total + '</span></strong> </td>';
                    cart_totals += '</tr>';
                    cart_totals += '</tbody></table>';
                    cart_totals += '<div class="wc-proceed-to-checkout">';
                    cart_totals += '<a href="http://transcentplugin.dev:8888/checkout/" class="checkout-button button alt wc-forward">';
                    cart_totals += 'Proceed to Checkout</a>';
                    cart_totals += '</div>';
                    cart_totals += '</div>';

                    jQuery('div.cart-collaterals').html(cart_totals);
                    cart_totals += '<tr class="order-total">';

                    }

                    if (response.sale_price_applied && jQuery.inArray(product_id, response.dice_thrown) !== -1) {

                        if(response.percentage  && response.percentage.spelled ) {

                            jQuery('.discount-title').css("visibility", "visible");
                            jQuery('#discount-block').html(response.percentage.spelled).css("visibility", "visible");
                            jQuery('.tpi-clear-discount').css("visibility", "visible");
                        }
                    }

                }
            }
        });
    }


    //catalog
    if(jQuery(catalog).length) {


        //reset flags here including:
        //  1. dice_thrown
        //  2.



        security = woocommerce_admin_meta_boxes_variations.save_variations_nonce;

        jQuery.ajax({
            url: tpiAjax.ajax_url,
            type: 'POST',//changing data on the server
            data: {
                action: 'dice_reset_flags',
                security: security, //get through $_POST['security']
            },
            success: function (response) {

                if (response != "0") {

                }
            }
        });

    }


    jQuery('#roll').on('click', function () {

        var AnimationEndSet = 'webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend';

        var currencySymbol = '<span class="woocommerce-Price-currencySymbol">$</span>';
        var price_seperator = '<span class="price-Seperator"> – </span>';

        setTimeout(function () {

            jQuery('#platform').addClass('rolling-platform');
            jQuery('#platform-2').addClass('rolling-platform');

            jQuery('#dice').addClass('rolling-dice');
            jQuery('#dice-2').addClass('rolling-dice');

            jQuery('#platform').one(AnimationEndSet, function () {
                jQuery(this).removeClass('rolling-platform');
                jQuery('#platform-2').removeClass('rolling-platform');
            });

            jQuery('#dice').one(AnimationEndSet, function () {
                jQuery(this).removeClass('rolling-dice');
                jQuery('#dice-2').removeClass('rolling-dice');
            });
        }, 500);
        /*  5-second delay */

        /*
         response:Object
         coupon_code:
         percentage.text:"12%"
         percentage.decimal: .12
         subtotal:39.98
         subtotal_ex_tax:39.98
         tax_total
         total:0
         */

        var current_page = jQuery('body');

        if (current_page) {
            if (jQuery('body').hasClass("post-type-archive-product"))//shop
            {
                /* get span.woocommerce-Price-currencySymbol  */
                /* get price and restyle with cross out   */
                /* add reduced price before taxes*/

                var product_price = [];
                var product_types = [];
                var product_ids = [];
                var list_items = jQuery('ul.products li.type-product').not('.sale');

                jQuery.each(list_items, function (index, current_list_item) {

                    var variation_price = [];
                    var price_set = jQuery(current_list_item).find('span.price').children();

                    if (jQuery(current_list_item).find('product-type-variable')) {
                        product_types.push('variable');
                    } else if (jQuery(current_list_item).find('product-type-simple')) {
                        product_types.push('simple');
                    }

                    var has_woocommerce_Price_amount = false;

                    jQuery.each(price_set, function (jindex, price) {

                        if (jQuery(price).hasClass('woocommerce-Price-amount')) {
                            has_woocommerce_Price_amount = true;
                            var amount = jQuery(price).text().replace('$', '');//does this work?
                            variation_price.push(amount); //add variation prices  tjs 2016Oct05
                        }
                    });

                    if (variation_price.length > 1) {

                        var original_prices =
                            '<del>' +
                            '<span class="woocommerce-Price-amount amount original-price dice-thrown">' +
                            '<span class="woocommerce-Price-currencySymbol">$</span>' +
                            variation_price[0] +
                            '</span>' +
                            price_seperator +
                            '<span class="woocommerce-Price-amount amount original-price dice-thrown">' +
                            '<span class="woocommerce-Price-currencySymbol">$</span>' +
                            variation_price[variation_price.length - 1] +
                            '</span>' +
                            '</del>';

                    } else if (variation_price.length = 1) {

                        var original_prices =
                            '<del>' +
                            '<span class="woocommerce-Price-amount amount original-price dice-thrown">' +
                            '<span class="woocommerce-Price-currencySymbol">$</span>' +
                            variation_price[0] +
                            '</span>' +
                            '</del>';
                    }

                    jQuery(current_list_item).find('span.price').html(original_prices);

                    var add_to_cart_button = jQuery(current_list_item).find('a.add_to_cart_button');

                    product_id = jQuery(add_to_cart_button).attr('data-product_id');
                    if (has_woocommerce_Price_amount) {
                        console.log('product_id has woocommerce-Price-amount:  ' + product_id);
                        product_ids.push(product_id);
                    }

                    product_price.push(variation_price); //add product prices  necessary to prices to back end tjs 2016Oct05
                });

                dice_thrown = product_ids;
                single_add_to_cart = false;

                security = woocommerce_admin_meta_boxes_variations.save_variations_nonce;//tjs security for nonce

                /************************************************************************************************/

                jQuery.ajax({
                    url: tpiAjax.ajax_url,
                    type: 'POST', /*changing data on the server */
                    data: {
                        action: 'tpi_cast_die_for_sale_price',
                        security: security, //get through $_POST['security']
                        product_price: product_price,//get through $_POST['product_price']
                        product_id: product_ids, //get through $_POST['product_id']
                        product_type: product_types, //get through $_POST['product_type']
                        dice_thrown: dice_thrown, //get through $_POST['dice_thrown']
                        dice_single_add_to_cart: single_add_to_cart, // $_POST['dice_single_add_to_cart']

                    },
                    success: function (response) {

                        if (response != "0") {

                            jQuery('.discount-title').css("visibility", "visible");
                            jQuery('#discount-block').html(response.percentage.spelled).css("visibility", "visible");
                            jQuery('.tpi-clear-discount').css("visibility", "visible");

                            /* add discount prices to shop page here.  tjs 2016Oct05 */
                            var discount_price = [];
                            var discount_price = null;
                            var terminus = null;

                            for (var li = 0; li < list_items.length; li++) {

                                var current_list_item = list_items[li];

                                if (!jQuery(current_list_item).hasClass('sale')) {

                                    if (response.product_price[li].discount_price) {
                                        if (response.product_price[li].discount_price.variation) {
                                            discount_price = response.product_price[li].discount_price.variation;
                                            terminus = response.product_price[li].discount_price.terminus;
                                        }
                                    }

                                    var discount_price_count = null;

                                    if (discount_price.length) {
                                        discount_price_count = discount_price.length;
                                    }

                                    if (discount_price_count > 1) {

                                        if (terminus) {
                                            var first_price = terminus['min'];
                                            var last_price = terminus['max'];

                                            var first_reduced_Price = jQuery(current_list_item).find('.first-Price');
                                            var last_reduced_Price = jQuery(current_list_item).find('.last-Price');
                                            var reduced_Price_Seperator = jQuery(current_list_item).find('.price-Seperator');

                                            if (first_reduced_Price.length) {
                                                first_reduced_Price.html(currencySymbol + first_price).addClass('dice-thrown');
                                                if (last_reduced_Price.length) {
                                                    reduced_Price_Seperator.data(' – ');
                                                    last_reduced_Price.html(currencySymbol + last_price).addClass('dice-thrown');
                                                }
                                            } else {

                                                jQuery(current_list_item).find('span.price').after(
                                                    '<span class="price">' +
                                                    '<span class=" reduced-Price first-Price amount dice-thrown">' +
                                                    currencySymbol + first_price + '</span>' + // .first-Price
                                                    price_seperator +
                                                    '<span class=" reduced-Price last-Price amount dice-thrown">' +
                                                    currencySymbol + last_price + '</span>' +
                                                    '</span>');
                                            }
                                        }

                                    } else {

                                        var discount_price = response.product_price[li].discount_price.variation[0];
                                        var single_Price = jQuery(current_list_item).find('.single-Price');

                                        if (single_Price.length) {
                                            single_Price.html(currencySymbol + discount_price);
                                        } else {

                                            jQuery(current_list_item).find('span.price').after(
                                                '<span class="price">' +
                                                '<span class=" reduced-Price single-Price amount dice-thrown">' +
                                                currencySymbol + discount_price + '</span>' +
                                                '</span>');
                                        }

                                    }

                                    jQuery(current_list_item).addClass('sale');
                                    jQuery(current_list_item).find('wp-post-image').before('<span class="onsale">Sale!</span>');


                                }// not class 'sale'

                            }//for(var li=0; li<list_items.length; li++)

                        }
                        /* response   */

                    },
                    error: function (errorThrown) {

                        console.log(errorThrown.toString());
                        console.log(tpiAjax.ajax_url);
                        console.log(errorThrown.statusText);
                        console.log(errorThrown.status);
                        console.log(errorThrown.responseText);
                        console.log(errorThrown.readyState);
                    }
                });
            }
            else if (jQuery('body').hasClass("single-product")) {
                var variation_price = [];
                var product_price = [];
                var prices = jQuery('div.entry-summary p.price').children();
                jQuery(prices).each(function (inner_index, price) {
                    if (jQuery(price).hasClass('woocommerce-Price-amount')) {
                        var amount = jQuery(price).text();
                        variation_price.push(amount);//add variation prices  tjs 2016Oct05
                    }
                });

                var variation_price_selected = jQuery('div.summary.entry-summary > form > div > div.woocommerce-variation.single_variation > div.woocommerce-variation-price > span.price > span.woocommerce-Price-amount.amount');

                var product_type = null;
                if (jQuery('div[itemtype="http://schema.org/Product"]').hasClass('product-type-variable')) {
                    //product_type = 'product-type-variable';
                    product_type = 'variable';// tjs 2017Jan01
                }

                var product_id = jQuery('form.variations_form').attr('data-product_id');
                //get product type?

                var selected_price_with_cursym = variation_price_selected.text();
                var variation_price_selected = selected_price_with_cursym.replace('$', '');

                product_price.push(variation_price);

                security = woocommerce_admin_meta_boxes_variations.save_variations_nonce;

                /************************************************************************************************/

                jQuery.ajax({
                    url: tpiAjax.ajax_url,
                    type: 'POST', /*changing data on the server */
                    data: {
                        action: 'tpi_cast_die_for_sale_price',
                        product_price: product_price,//get through $_POST['product_price']
                        selected_price: variation_price_selected, //get through $_POST['selected_price']
                        security: security, //get through $_POST['security']
                        product_id: product_id, //get through $_POST['product_id']
                        product_type: product_type,//get through $_POST['product_type']

                    },
                    success: function (response) {

                        if (response != "0") {

                            jQuery('.discount-title').css("visibility", "visible");
                            jQuery('#discount-block').html(response.percentage.spelled).css("visibility", "visible");
                            jQuery('.tpi-clear-discount').css("visibility", "visible");

                            var new_price = response.new_price;

                            jQuery('div[itemprop="offers"]').html(new_price);

                            var discount_prices = [];
                            discount_prices = response.product_price.discount_price.variation;

                            var discount_price_count = discount_prices.length;

                            if (discount_price_count > 1) {

                                var first = discount_prices[0];
                                var last = discount_prices[discount_price_count - 1];

                                var first_original_Price_field = jQuery('div[itemprop="offers"] > p.price > .original-Price.first-Price.amount');
                                var last_original_Price_field = jQuery('div[itemprop="offers"] > p.price > .original-Price.last-Price.amount');
                                var reduced_Price_Seperator = jQuery('div[itemprop="offers"] > p.price >.price-Seperator');

                                var first_active_price_field = null;
                                var last_active_price_field = null;

                            } else if (discount_price_count == 1) {

                                var first_reduced_Price = jQuery('div[itemprop="offers"] > p.price > .single-Price.amount');

                                if (first_reduced_Price.length) {
                                    first_reduced_Price.html(currencySymbol + discount_prices[0]);
                                } else {
                                    jQuery('div[itemprop="offers"] > p.price').after(
                                        '<p class="price">' +
                                        '<span class="reduced-Price single-Price amount dice-thrown">' +
                                        currencySymbol + discount_prices[0] +
                                        '</span>' +
                                        '</p>');
                                }

                            }

                            var selected_variation_price = response.product_price.selected_variation_price;

                            if (selected_variation_price) {

                                var woocommerce_variation_price = '<span class="price">' +
                                    '<del>' +
                                    '<span class="woocommerce-Price-amount amount dice-thrown">' +
                                    '<span class="woocommerce-Price-currencySymbol">$</span>' +
                                    variation_price_selected +
                                    '</span>' +
                                    '</del>' +

                                    '<ins>' +
                                    '<span class="woocommerce-Price-amount amount dice-thrown">' +
                                    '<span class="woocommerce-Price-currencySymbol">$</span>' +
                                    selected_variation_price +
                                    '</span>' +
                                    '</ins>' +
                                    '</span>';

                                jQuery('div.woocommerce-variation-price').html(woocommerce_variation_price);

                            }

                            if (full_Price_field.length) {
                                full_Price_field.html(currencySymbol + selected_variation_price);
                            }

                        }
                        /* response   */

                    },
                    error: function (errorThrown) {
                        console.log(errorThrown);
                        console.log(tpiAjax.ajax_url);
                        alert('Error: single-product class');
                    }
                });

            }
            else if (jQuery('body').hasClass("woocommerce-cart")) {
                jQuery('.discount-title').css("visibility", "visible");
                jQuery('#discount-span').empty(); //remove value to prepare for new value. tjs 2016Oct20
                jQuery('.tpi-clear-discount').css("visibility", "visible");

                jQuery('.cart-discount > th').data("Coupon:  ");
                jQuery('.negative-sign').empty();
                jQuery('.woocommerce-remove-coupon').empty();
                jQuery('.reduced-Price').empty(); //remove value to prepare for new value. tjs 2016Oct20

                jQuery.ajax({
                    url: tpiAjax.ajax_url,
                    type: 'POST', /*changing data on the server */
                    data: {
                        action: 'tpi_cast_die_for_coupon',
                    },
                    success: function (response) {

                        if (response != "0") {

                            jQuery('.discount-title').css("visibility", "visible");
                            jQuery('#discount-block').html(response.percentage.spelled).css("visibility", "visible");
                            jQuery('.tpi-clear-discount').css("visibility", "visible");

                            var coupon = '<tr class="cart-discount coupon-' + response.coupon_code + '">';
                            coupon += '<th>Coupon:\n ' + response.coupon_code + '</th>';
                            coupon += '<td>-<span class="woocommerce-Price-amount amount reduced-Price"><span class="woocommerce-Price-currencySymbol">$</span>' + response.discount + '</span> <a href="http://transcentplugin.dev:8888/checkout/?remove_coupon=' + response.coupon_code + '" class="woocommerce-remove-coupon" data-coupon="f' + response.coupon_code + '">[Remove]</a></td>';
                            coupon += '</tr>';
                            jQuery('tr.cart-subtotal').after(coupon);

                            var tax = '<th>Tax</th>';
                            tax += '<td><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">$</span>' + response.tax_total + '</span></td>';
                            jQuery('tr.tax-total').html(tax);

                            var total = '<th>Total</th>';
                            total += '<td><strong><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">$</span>' + response.order_total + '</span></strong> </td>';
                            jQuery('tr.order-total').html(total);


                            var cart_totals = '<div class="cart_totals calculated_shipping">';
                            cart_totals += '<h2>Cart Totals</h2>';
                            cart_totals += '<table cellspacing="0" class="shop_table shop_table_responsive">';
                            cart_totals += '<tbody><tr class="cart-subtotal">';
                            cart_totals += '<th>Subtotal</th>';
                            cart_totals += '<td data-title="Subtotal"><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">$</span>' + response.subtotal + '</span></td> </tr>';
                            cart_totals += '<tr class="cart-discount coupon-' + response.coupon_code + '">';
                            cart_totals += '<th>Coupon:\n' + response.coupon_code + '</th>';
                            cart_totals += '<td data-title="Coupon: ' + response.coupon_code + '"><span class="negative-sign">-</span><span class="woocommerce-Price-amount amount reduced-Price"><span class="woocommerce-Price-currencySymbol">$</span>' + response.discount + '</span> <a href="http://transcentplugin.dev:8888/cart/?remove_coupon=' + response.coupon_code + ' " class="woocommerce-remove-coupon" data-coupon="' + response.coupon_code + '">[Remove]</a></td>';
                            cart_totals += '</tr>';
                            cart_totals += '<tr class="tax-total">';
                            cart_totals += '<th>Tax</th>';
                            cart_totals += '<td data-title="Tax"><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">$</span>' + response.tax_total + '</span></td>';
                            cart_totals += '</tr>';
                            cart_totals += '<th>Total</th>';
                            cart_totals += '<td data-title="Total"><strong><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">$</span>' + response.order_total + '</span></strong> </td>';
                            cart_totals += '</tr>';
                            cart_totals += '</tbody></table>';
                            cart_totals += '<div class="wc-proceed-to-checkout">';
                            cart_totals += '<a href="http://transcentplugin.dev:8888/checkout/" class="checkout-button button alt wc-forward">';
                            cart_totals += 'Proceed to Checkout</a>';
                            cart_totals += '</div>';
                            cart_totals += '</div>';

                            jQuery('div.cart-collaterals').html(cart_totals);
                            cart_totals += '<tr class="order-total">';

                        }
                        /* response   */

                    },
                    error: function (errorThrown) {
                        console.log(errorThrown);
                        console.log(tpiAjax.ajax_url);
                        alert('Error: woocommerce-cart class');
                    }
                })
            }
            else if (jQuery('body').hasClass("woocommerce-checkout")) {
                console.log('Checkout New Check');

                jQuery.ajax({
                    url: tpiAjax.ajax_url,
                    type: 'POST', /*changing data on the server */
                    data: {
                        action: 'tpi_cast_die_for_coupon',

                    },
                    success: function (response) {

                        if (response != "0") {

                            jQuery('.discount-title').css("visibility", "visible");
                            jQuery('#discount-block').html(response.percentage.spelled).css("visibility", "visible");
                            jQuery('.tpi-clear-discount').css("visibility", "visible");

                            var coupon = '<tr class="cart-discount coupon-' + response.coupon_code + '">';
                            coupon += '<th>Coupon:\n ' + response.coupon_code + '</th>';
                            coupon += '<td>-<span class="woocommerce-Price-amount amount reduced-Price"><span class="woocommerce-Price-currencySymbol">$</span>' + response.discount + '</span> <a href="http://transcentplugin.dev:8888/checkout/?remove_coupon=' + response.coupon_code + '" class="woocommerce-remove-coupon" data-coupon="f' + response.coupon_code + '">[Remove]</a></td>';
                            coupon += '</tr>';
                            jQuery('tr.cart-subtotal').after(coupon);

                            var tax = '<th>Tax</th>';
                            tax += '<td><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">$</span>' + response.tax_total + '</span></td>';
                            jQuery('tr.tax-total').html(tax);

                            var total = '<th>Total</th>';
                            total += '<td><strong><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">$</span>' + response.order_total + '</span></strong> </td>';
                            jQuery('tr.order-total').html(total);

                        }
                        /* response   */
                    },
                    error: function (errorThrown) {
                        console.log(errorThrown);
                        console.log(tpiAjax.ajax_url);
                        alert('Error: woocommerce-checkout class');
                    }
                });
            }
        }

        return false;
    });//jQuery('#roll').on('click', function () {


    jQuery('a.tpi-clear-discount').on('click', function () {

        jQuery.ajax({
            url: tpiAjax.ajax_url,
            type: 'POST',//changing data on the server
            data: {
                action: 'clear_discount'
            },
            success: function (response) {

                if (response == "0") {

                    jQuery('.discount-title').css("visibility", "hidden");
                    jQuery('#discount-block').empty().css("visibility", "hidden");
                    jQuery('.tpi-clear-discount').css("visibility", "hidden");

                    console.log('clicked tpi-clear-discount');

                    if (jQuery('body').hasClass("post-type-archive-product")) {
                        var original_price = jQuery('.woocommerce-Price-amount');
                        original_price.css('text-decoration', 'none');
                        jQuery('.reduced-Price').empty();
                        jQuery('.price-Seperator').empty();
                    }

                } //response
            },
            error: function (errorThrown) {
                console.log(errorThrown);
                console.log(tpiAjax.ajax_url);
                alert('Error:' + errorThrown.toString());
            }
        });

        return false;
    });


    jQuery('button.single_add_to_cart_button').on('click', function () {

        if (jQuery('body').hasClass("single-product")) {
            single_add_to_cart = true;

            // How do I know if dice have been thrown, that I have del and ins?  can I just assume?
            original_variation_price_selected = jQuery('.woocommerce-variation-price > .price > del > .woocommerce-Price-amount');
            discounted_variation_price_selected = jQuery('.woocommerce-variation-price > .price > ins > .woocommerce-Price-amount');

            var product_id = jQuery('form.variations_form').attr('data-product_id');

            security = woocommerce_admin_meta_boxes_variations.save_variations_nonce;

            jQuery.ajax({
                url: tpiAjax.ajax_url,
                type: 'POST', //changing data on the server
                data: {
                    action: 'retain_selection',
                    original_variation_price_selected: original_variation_price_selected.text().replace('$', ''),//tjs 2017May04
                    discounted_variation_price_selected: discounted_variation_price_selected.text().replace('$', ''),
                    security: security,
                    product_id: product_id, //get through $_POST['product_id']
                    dice_single_add_to_cart: single_add_to_cart,
                },
                success: function (response) {


                    if (response != "0") {

                        jQuery('.discount-title').css("visibility", "visible");
                        jQuery('#discount-block').html(response.percentage.spelled).css("visibility", "visible"); //temporarily deactivated tjs 2017April25
                        jQuery('.tpi-clear-discount').css("visibility", "visible");

                        //if dice_thrown
                        if (response.dice_single_add_to_cart === 'true') {

                            //add styling for original and discounted prices

                            if (response.dice_thrown) {
                                var currencySymbol = '<span class="woocommerce-Price-currencySymbol">$</span>';
                                var sale_price_block =
                                    '<ins>' +
                                    '<span class="woocommerce-Price-amount amount">' +
                                    currencySymbol + response.discounted_variation_price_selected +
                                    '</span>' +
                                    '</ins>';
                            }
                        }

                        // response
                    }

                },
                error: function (errorThrown) {
                    console.log(errorThrown.toString());
                    //console.log(tpiAjax.ajax_url);
                    console.log(errorThrown.statusText);
                    console.log(errorThrown.status);
                    console.log(errorThrown.responseText);
                    console.log(errorThrown.readyState);
                }
            });

        }
        //}, 10000); //  5-second delay


    });


    jQuery('.checkout-button').on('click', function () {


        security = woocommerce_admin_meta_boxes_variations.save_variations_nonce;

        jQuery.ajax({
            url: tpiAjax.ajax_url,
            type: 'POST', /*changing data on the server */
            data: {
                //action : 'get_sale_prices_ajax',
                action: 'delete_dice_thrown_option',
            },
            success: function (response) {

                //option deleted
                console.log("option dice_thrown deleted.");

            },
            error: function (errorThrown) {
                console.log(errorThrown.toString());
                //console.log(tpiAjax.ajax_url);
                console.log(errorThrown.statusText);
                console.log(errorThrown.status);
                console.log(errorThrown.responseText);
                console.log(errorThrown.readyState);
            }
        });


    });



    jQuery('#place_order').on('click', function () {

        //delete_option( $option );

        security = woocommerce_admin_meta_boxes_variations.save_variations_nonce;

        jQuery.ajax({
            url: tpiAjax.ajax_url,
            type: 'POST', /*changing data on the server */
            data: {
                //action : 'get_sale_prices_ajax',
                action: 'delete_dice_thrown_option',
            },
            success: function (response) {

                //option deleted
                console.log("option dice_thrown deleted.");

            },
            error: function (errorThrown) {
                console.log(errorThrown.toString());
                //console.log(tpiAjax.ajax_url);
                console.log(errorThrown.statusText);
                console.log(errorThrown.status);
                console.log(errorThrown.responseText);
                console.log(errorThrown.readyState);
            }
        });
    });


    jQuery('.variations select').one('click', function () {

        jQuery('.variations select').change(function () {

            //dice thrown
            //product not yet added to cart
            // Is this information relevant when selecting a variation?

            var i = jQuery('.variations select').prop('selectedIndex');

            if (i > 0) {

                //get product id.
                var product_id = jQuery('form.variations_form').attr('data-product_id');
                var dice_flag = 'select_saleprice';
                var variation_changed = true;// needed for when the .single-product form is refreshed.

                security = woocommerce_admin_meta_boxes_variations.save_variations_nonce;

                jQuery.ajax({
                    url: tpiAjax.ajax_url,
                    type: 'POST', /*changing data on the server */
                    data: {
                        action: 'tpi_get_dice_saleprice',
                        security: security,//'woocommerce_admin_meta_boxes_variations.save_variations_nonce' ,
                        dice_index: i, //get through $_POST['product_id']
                        dice_product_id: product_id,
                        dice_flag: dice_flag,
                        dice_variation_changed: variation_changed,
                    },
                    success: function (response) {

                        if(response.dice_thrown){

                            var sale_price_block =
                                '<ins>' +
                                '<span class="woocommerce-Price-amount amount">' +
                                '<span class="woocommerce-Price-currencySymbol">$</span>' +
                                response.discounted_variation_price_selected +
                                '</span>' +
                                '</ins>';

                            jQuery('div.woocommerce-variation.single_variation div.woocommerce-variation-price span.price span.woocommerce-Price-amount.amount').wrap("<del></del>");
                            jQuery('div.woocommerce-variation.single_variation div.woocommerce-variation-price span.price').append(sale_price_block);

                        }

                    },
                    error: function (errorThrown) {
                        console.log(errorThrown.toString());
                        //console.log(tpiAjax.ajax_url);
                        console.log(errorThrown.statusText);
                        console.log(errorThrown.status);
                        console.log(errorThrown.responseText);
                        console.log(errorThrown.readyState);
                    }
                });
            }

        });

    });
});





