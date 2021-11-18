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
        <h2>Order #<span id="data-order_no">1</span></h2>

        <!-- Order Details -->
        <div class="sale-container">
            <accordian :title="'{{ __('admin::app.sales.orders.order-and-account') }}'" :active="true">
                <div slot="body">

                    <div class="sale-section">
                        <div class="secton-title">
                            <span>{{ __('admin::app.sales.orders.order-info') }}</span>
                        </div>

                        <div class="section-content">
                            <div class="row">
                                <span class="title">
                                    {{ __('admin::app.sales.orders.order-date') }}
                                </span>

                                <span id="data-order_date" class="value">
                                </span>
                            </div>

                            <div class="row">
                                <span class="title">
                                    {{ __('admin::app.sales.orders.order-status') }}
                                </span>

                                <span id="data-order_status" class="value">
                                </span>
                            </div>

                            <div class="row">
                                <span class="title">
                                    {{ __('admin::app.sales.orders.channel') }}
                                </span>

                                <span id="data-order_channel_name" class="value">
                                </span>
                            </div>

                        </div>
                    </div>

                    <div class="sale-section">
                        <div class="secton-title">
                            <span>{{ __('admin::app.sales.orders.account-info') }}</span>
                        </div>

                        <div class="section-content">
                            <div class="row">
                                <span class="title">
                                    {{ __('admin::app.sales.orders.customer-name') }}
                                </span>

                                <span id="data-customer_full_name" class="value">
                                </span>
                            </div>

                            <div class="row">
                                <span class="title">
                                    {{ __('admin::app.sales.orders.email') }}
                                </span>

                                <span id="data-customer_email" class="value">
                                </span>
                            </div>
                        </div>
                    </div>

                </div>
            </accordian>

            <accordian :title="'{{ __('admin::app.sales.orders.address') }}'" :active="true">
                <div slot="body">

                    <div class="sale-section">
                        <div class="secton-title">
                            <span>{{ __('admin::app.sales.orders.billing-address') }}</span>
                        </div>

                        <div id="data-billing_address" class="section-content">
                        </div>
                    </div>

                    <div class="sale-section">
                        <div class="secton-title">
                            <span>{{ __('admin::app.sales.orders.shipping-address') }}</span>
                        </div>

                        <div id="data-shipping_address" class="section-content">
                        </div>
                    </div>

                </div>
            </accordian>

            <accordian :title="'{{ __('admin::app.sales.orders.payment-and-shipping') }}'" :active="true">
                <div slot="body">

                    <div class="sale-section">
                        <div class="secton-title">
                            <span>{{ __('admin::app.sales.orders.payment-info') }}</span>
                        </div>

                        <div class="section-content">
                            <div class="row">
                                <span class="title">
                                    {{ __('admin::app.sales.orders.payment-method') }}
                                </span>

                                <span id="data-payment_method" class="value">
                                </span>
                            </div>

                            <div class="row">
                                <span class="title">
                                    {{ __('admin::app.sales.orders.currency') }}
                                </span>

                                <span id="data-order_currency" class="value">
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="sale-section">
                        <div class="secton-title">
                            <span>{{ __('admin::app.sales.orders.shipping-info') }}</span>
                        </div>

                        <div class="section-content">
                            <div class="row">
                                <span class="title">
                                    {{ __('admin::app.sales.orders.shipping-method') }}
                                </span>

                                <span id="data-shipping_method" class="value">
                                </span>
                            </div>

                            <div class="row">
                                <span class="title">
                                    {{ __('admin::app.sales.orders.shipping-price') }}
                                </span>

                                <span id="data-shipping_price" class="value">
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </accordian>

            <accordian :title="'{{ __('admin::app.sales.orders.products-ordered') }}'" :active="true">
                <div slot="body">

                    <div class="table">
                        <table id="data-table_items">
                            <thead>
                                <tr>
                                    <th>{{ __('admin::app.sales.orders.SKU') }}</th>
                                    <th>{{ __('admin::app.sales.orders.product-name') }}</th>
                                    <th>{{ __('admin::app.sales.orders.price') }}</th>
                                    <th>{{ __('admin::app.sales.orders.item-status') }}</th>
                                    <th>{{ __('admin::app.sales.orders.subtotal') }}</th>
                                    <th>{{ __('admin::app.sales.orders.tax-percent') }}</th>
                                    <th>{{ __('admin::app.sales.orders.tax-amount') }}</th>
                                    <th>{{ __('admin::app.sales.orders.grand-total') }}</th>
                                </tr>
                            </thead>

                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </accordian>
        </div>
        <!-- Modal Footer -->
        <div class="cashier-modal-footer">
            <button id="bCancel" type="button" class="btn btn-lg btn-primary"
                style="background-color: orange;">Cancel</button>
            <button id="bRefund" type="button" class="btn btn-lg btn-primary"
                style="background-color: orange;">Refund</button>
            <button id="bInvoice" type="button" class="btn btn-lg btn-primary" style="background-color: orange;"
                onclick="InvoiceOrder('{{ url('admin/sales/invoices/create/') }}');">Invoice</button>
            <button id="bDeliver" type="button" class="btn btn-lg btn-primary"
                style="background-color: orange;">Deliver</button>
            <button id="bPrint" type="button" class="btn btn-lg btn-primary"
                style="background-color: green;">Print</button>
        </div>
    </div>

</div>
@stop