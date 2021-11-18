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

            result.items.forEach(element => {
                let discount_row;
                if (element.base_discount_amount > 0) discount_row = '<td id="d-base_discount_amount">' + element.base_discount_amount + '</td>';
                var grand_total = parseFloat(element.base_total) + parseFloat(element.base_tax_amount) - parseFloat(element.base_discount_amount);
                $('#data-table_items tbody').append(
                    `
                    <td id="d-sku">` + element.sku + `</td>
                    <td id="d-name">` + element.name + `</td>
                    <td id="d-base_price">` + parseFloat(element.base_price).toFixed(2) + `</td>
                    <td id="d-qty_ordered">Ordered (` + element.qty_ordered + `)</td>
                    <td id="d-base_total">` + parseFloat(element.base_total).toFixed(2) + `</td>
                    <td id="d-tax_percent">` + element.tax_percent + `</td>
                    <td id="d-base_tax_amount">` + parseFloat(element.base_tax_amount).toFixed(2) + `</td>
                    ` + discount_row + `
                    <td id="d-grand_total">` + grand_total.toFixed(2) + `</td>
                `);
            });

            if (result.status == "pending") {
                $('#bCancel').show();
                $('#bRefund').hide();
                $('#bInvoice').show();
                $('#bDeliver').hide();
                $('#bPrint').hide();
            }

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


function CancelOrder(data_url) {

}


function InvoiceOrder(data_url) {
    console.log(data_url);

    // Create an Invoice
    $.ajax({
        url: data_url,
        type: "GET",
        success: function (result) {


        },
        error: function (result) {
            console.error(result.responseJSON.status);
        }
    });
}