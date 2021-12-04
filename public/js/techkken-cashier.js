let order_result;

function toggleNavBarLeft() {
    $(".navbar-left").toggle();
    if ($(".content-container").css('padding-left') == '90px')
        $(".content-container").css({
            'padding-left': '0px'
        });
    else
        $(".content-container").css({
            'padding-left': '90px'
        });
}

function viewOrder(data_url) {
    console.log(data_url);

    // Retrieve Order Details
    $.ajax({
        url: data_url,
        type: "GET",
        success: function (result) {
            console.log(result);
            order_result = result;
            console.log("GET Order Detail...");

            // Load Order Details in Modal
            $('#data-order_no').text(result.id);
            dt = new Date(result.created_at);
            $('#data-order_date').text(dt.toDateString());
            $('#data-order_status').text(result.status.charAt(0).toUpperCase() + result.status.slice(1));
            $('#data-order_channel_name').text(result.channel_name);

            $('#data-customer_full_name').text(result.customer_first_name + ' ' + result.customer_last_name);
            $('#data-customer_email').text(result.customer_email);

            result.addresses.forEach(element => {
                console.log(element);
                if (element.address_type == 'order_billing') {
                    $('#data-billing_address').html(
                        element.company_name + `<br>
                        <b>` + element.first_name + ` ` + element.last_name + `</b><br>` +
                        element.address1 + `<br>` +
                        element.postcode + ` ` + element.city + `<br>` +
                        element.state + `<br></br>
                        Contact : ` + element.phone);
                } else if (element.address_type == 'order_shipping') {
                    $('#data-shipping_address').html(
                        element.company_name + `<br>
                        <b>` + element.first_name + ` ` + element.last_name + `</b><br>` +
                        element.address1 + `<br>` +
                        element.postcode + ` ` + element.city + `<br>` +
                        element.state + `<br></br>
                        Contact : ` + element.phone);
                }
            });

            let method_name = "";
            switch (result.payment.method) {
                case "cashondelivery":
                    method_name = "Cash On Delivery";
                    break;
                case "moneytransfer":
                    method_name = "Money Transfer";
                    break;
                case "gcash":
                    method_name = "GCash";
                    break;
            }
            $('#data-payment_method').text(method_name);
            $('#data-order_currency').text(result.order_currency_code);

            $('#data-shipping_method').text(result.shipping_title);
            $('#data-shipping_method').text(result.shipping_price);

            $('#data-table_items tbody').empty();
            result.items.forEach(element => {
                var grand_total = parseFloat(element.base_total) + parseFloat(element.base_tax_amount) - parseFloat(element.base_discount_amount);
                $('#data-table_items tbody').append(
                `<tr>
                    <td id="d-qty_ordered">` + element.qty_ordered + `</td>
                    <td id="d-name">` + element.name + `</td>
                    <td id="d-grand_total">` + grand_total.toFixed(2) + `</td>
                </tr>`);
            });

            $('#data-sub_total').text(result.invoices[0].sub_total);
            $('#data-delivery_fee').text(result.invoices[0].shipping_amount);
            $('#data-grand_total').text(result.invoices[0].grand_total)

            // Show Modal
            $("#cashier-modal").show();

            /* $("#edit_id").val(id);
            $("#edit_type").val(result.type);
            $("#edit_content").val(result.content); */
        },
        error: function (result) {
            console.error(result.responseJSON.status);
        }
    });

}

function closeOrder() {
    $("#cashier-modal").hide();
}


