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
                <div><i>{{$order->addresses[0]->address1??'No Address'}}</i></div>
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
                P10000
            </div>
            <div class="cashier-row-action">
                <div class="action-view" onclick="viewOrder('{{ route('cashier.viewOrder', [$order->id]) }}');"><span
                        class="icon eye-icon"></span></div>
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
            <a href="http://localhost:8080/admin/sales/invoices/print/{{$order->invoices[0]->id}}" ><button type="submit" class="btn btn-lg btn-primary" style="background-color: green;">Print</button></a>
        </div>
        <p><span>Date:</span><span>{{ date("m/d/Y",strtotime($order->created_at))}}</span></p>
        <p><span>Time:</span><span>{{ date("H:i:A",strtotime($order->created_at))}}</span></p>
        <p><span>Customer:</span><span>{{$order->customer_full_name}}</span></p>
        <p><span>Payment Method:</span><span>{{ core()->getConfigData('sales.paymentmethods.' . $order->payment->method . '.title')}}</span></p>
        <hr>
        <p>Ordered Items:</p>
        <ul>
        @foreach($order->items as $item)
        <li>
            <str>x&nbsp;</span><span>{{$item->qty_ordered}}</span>
            <span>{{$item->name}}</span>
            <span>&nbsp;{{number_format($item->base_total,2)}}</span> 
        </li>
        

        @endforeach
        </ul>
        <hr>
        <p><span>Subtotal:</span><span>{{number_format($order->base_sub_total,2)}}</span></p>
        <p><span>Delivery Fee:</span><span>{{number_format($order->base_shipping_amount,2)}}</span></p>
        <p><span>Grand Total:</span><span>{{number_format($order->base_grand_total_invoiced,2)}}</span></p>
    </div>

</div>
@stop