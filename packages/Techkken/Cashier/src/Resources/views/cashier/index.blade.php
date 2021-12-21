@extends('techkken::layouts.content')

@section('page_title')
{{ __('techkken::app.techkken.cashier.title') }}
@stop

@section('content')
<section class="cashier-orders">
    <div class="cashier-header">
        <button onclick="toggleNavBarLeft();"><img class="bars-icon" /></button>
        <h1 class="cashier-title">Cashier</h1>
    </div>

    <!-- <form class="form-inline cashier-search" action="{{ route('cashier.index') }}" method="get">
        <label for="searching">Filter:</label>
        <input type="text" placeholder="Enter Order Number" id="order_number" name="order_number">
        <input type="text" placeholder="Enter search keyword" id="search_keyword" name="search_keyword">
        <select id="status" name="status">
            <option value="None">None</option>
            <option value="Pending" selected>Pending</option>
        </select>
        <input type="date" placeholder="Start Date" id="date_start" name="date_start">
        <input type="date" placeholder="End Date" id="date_end" name="date_end">
        <button type="submit">Search</button>
    </form> -->


    <div class="cashier-order-list">

        @foreach ($orders as $order)
        <div class="cashier-row-card">
            <div class="cashier-row-order-no">
                <div class="cashier-order-no">#<span>{{$order->id}}</span></div>
            </div>
            <div class="cashier-left-border"></div>
            <div class="cashier-row-info">
                <div><b>{{$order->customer_full_name}}</b></div>
                <div><i>{{$order->addresses[0]->address1?:'No Address'}}</i></div>
                <div>
                    @switch($order->status)
                    @case("pending")
                    <span class="badge badge-md badge-warning">{{ucfirst($order->status)}}</span>
                    @break
                    @case("completed")
                    <span class="badge badge-md badge-success">{{ucfirst($order->status)}}</span>
                    @break

                    @default

                    @endswitch
                </div>
            </div>
            <div class="cashier-row-total">
                PHP. {{number_format($order->base_grand_total_invoiced,2)}}
            </div>
            <div class="cashier-row-action">
                <div class="action-view" onclick="viewOrder('{{ route('cashier.viewOrder', [$order->id]) }}');"><span class="icon eye-icon"></span></div>
                <a class="action-process" href="{{ route('cashier.processOrder', [$order->id]) }}"><span class="icon import-icon"></span></a>
            </div>
        </div>
        @endforeach

    </div>
    {{ $orders->links() }}
</section>

<div id="cashier-modal" class="cashier-modal">
    <!-- Modal content -->
    <div class="cashier-modal-content">
        <span class="cashier-close" onclick="closeOrder()">&times;</span>
        <!-- Order Details -->
        <!-- Modal Footer -->
        <h2>Order #<span id="data-order_no">1</span></h2>
        <div class="cashier-modal-footer">
            <button type="submit" class="btn btn-lg btn-primary" style="background-color: orange;">Cancel</button>
            <button type="submit" class="btn btn-lg btn-primary" style="background-color: green;" onclick="GeneratePDF();">Print</button></a>
        </div>
        <p><span>Date:</span><span id="data-order_date">&nbsp;</span></p>
        <p><span>Time:</span><span id="data-order_time">&nbsp;</span></p>
        <p><span>Customer:</span><span id="data-customer_full_name">&nbsp;</span></p>
        <p><span>Status:</span><span id="data-order_status">&nbsp;</span></p>
        <p><span>Payment Method:</span><span id="data-payment_method">&nbsp;</span></p>
        <hr>
        <p>Ordered Items:</p>
        <table id="data-table_items">
            <thead>
                <th>QTY</th>
                <th>Item Name</th>
                <th>Price</th>
            </thead>
            <tbody>
            </tbody>
        </table>
        <hr>
        <p><span>Subtotal:</span><span id="data-sub_total">&nbsp;</span></p>
        <p><span>Delivery Fee:</span><span id="data-delivery_fee">&nbsp;</span></p>
        <p><span>Grand Total:</span><span id="data-grand_total">&nbsp;</span></p>
    </div>

</div>


<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.0.0-rc.7/dist/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/dompurify/2.2.7/purify.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.3.1/jspdf.umd.min.js"></script>
<script src="https://unpkg.com/jspdf-autotable@3.5.14/dist/jspdf.plugin.autotable.js"></script>
<script src="{{ asset('js/Tahoma Regular font-normal.js') }}"></script>;

<script type="text/javascript">
    window.jsPDF = window.jspdf.jsPDF;

    function GeneratePDF(order) {
        console.log(order_result);

        var doc = new jsPDF('p', 'mm', [44, 100]);

        // Add Font...
        doc.addFileToVFS('Tahoma Regular font-normal.ttf', font);
        doc.addFont('Tahoma Regular font-normal.ttf', 'Tahoma Regular font', 'normal');

        // Build PDF here...
        console.log(doc.getFontList());
        //doc.setFont('courier');
        doc.setFont('Tahoma Regular font', "normal");
        doc.setFontSize(8);

        doc.text(order_result.channel_name, 22, 4, 'center');
        //doc.setFontSize(6);
        doc.text("Thank you for your purchase!", 22, 7, 'center');
        doc.text("Tel No: (02) 8888-8888", 22, 10, 'center');

        //doc.text("***************************************************", 22, 10, 'center');
        var topY = 16;
        doc.text("Order #: " + order_result.id, 0, topY);
        var_date = new Date(order_result.created_at);
        topY += 3;
        doc.text("Date: " + var_date.toLocaleDateString(), 0, 19);
        topY += 3;
        doc.text("Time: " + var_date.toLocaleTimeString(), 0, 22);
        topY += 3;
        doc.text("Customer: " + order_result.customer_first_name + " " + order_result.customer_last_name, 0, 25);
        topY += 3;
        doc.text("Address: ", 0, topY);
        topY += 3;
        var full_address = order_result.addresses[0].address1 + ", " + order_result.addresses[0].city;
        var address_lines = doc.splitTextToSize(full_address, 46);
        address_lines.forEach(element => {
            console.log(element);
            doc.text(element, 1, topY);
            topY += 3;
        });

        doc.text("Contact #: " + order_result.addresses[0].phone, 0, topY);
        topY += 3;

        let method_name = "";
        switch (order_result.payment.method) {
            case "cashondelivery":
                method_name = "Cash On Delivery";
                break;
            case "moneytransfer":
                method_name = "Money Transfer";
                break;
            case "paypal":
                method_name = "Paypal";
                break;
            case "gcash":
                method_name = "GCash";
                break;
        }
        doc.text("Payment: " + method_name, 0, topY);
        topY += 2;

        doc.line(0, topY, 44, topY);
        topY += 3;

        // ORDER LIST
        var serverY = topY;

        var counter = 0;
        order_result.items.forEach(element => {
            var grand_total = parseFloat(element.base_total) + parseFloat(element.base_tax_amount) - parseFloat(element.base_discount_amount);
            doc.text("x" + element.qty_ordered, 0, serverY);

            var item_lines = doc.splitTextToSize(element.name, 40);
            item_lines.forEach(item => {
                doc.text(item, 4, serverY);
                serverY += 3;
            });


            doc.text("P" + grand_total.toFixed(2), 43, serverY, 'right');
            serverY += 3;
            counter++;
        });

        var clientY = serverY

        /* clientY = clientY + 1;
        doc.text(counter + " item(s)", 0, clientY); */

        clientY = clientY + 6;
        doc.text("Sub-Total: ", 10, clientY);
        doc.text("P", 27, clientY);

        doc.text(parseFloat(order_result.invoices[0].sub_total).toFixed(2), 43, clientY, 'right');

        clientY = clientY + 3;
        doc.text("Delivery Fee: ", 10, clientY);
        doc.text("P", 27, clientY);
        doc.text(parseFloat(order_result.shipping_amount).toFixed(2), 43, clientY, 'right');

        clientY = clientY + 3;
        doc.text("Tax Fee: ", 10, clientY);
        doc.text("P", 27, clientY);
        doc.text(parseFloat(order_result.tax_amount).toFixed(2), 43, clientY, 'right');

        clientY = clientY + 4;
        doc.text("Grand Total: ", 10, clientY);
        doc.text("P", 27, clientY);
        doc.setFontSize(10);
        doc.text(parseFloat(order_result.grand_total).toFixed(2), 43, clientY, 'right');

        doc.setFontSize(8);
        clientY = clientY + 4;
        doc.line(0, clientY, 44, clientY);
        clientY = clientY + 3;
        doc.text("Cashier: {{ auth()->guard('admin')->user()->name }}", 0, clientY);
        clientY = clientY + 3;
        doc.text("Valid Until <?php echo Carbon\Carbon::now()->addYear(); ?>", 0, clientY);
        clientY = clientY + 5;
        doc.text("Notes: ", 0, clientY);
        clientY = clientY + 3;
        console.log(order_result.comments);
        if (order_result.comments.length !== 0) {
            var customer_comment = doc.splitTextToSize(order_result.comments[0].comment, 46);
            customer_comment.forEach(comment => {
                console.log(comment);
                doc.text(comment, 1, clientY);
                clientY += 3;
            });
        }
        else {
            doc.text("Customer do not have notes.", 1, clientY);
            clientY += 3;
        }

        //doc.text(, 0, clientY);

        //doc.save("OR_" + order_result.id + ".pdf");
        doc.autoPrint();
        doc.output("dataurlnewwindow");
    }
</script>
@stop