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
                <div class="action-view" onclick="viewOrder('{{ route('cashier.viewOrder', [$order->id]) }}');"><span class="icon eye-icon"></span></div>
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
                                    {{ $order->created_at }}
                                </span>
                            </div>

                            {!! view_render_event('sales.order.created_at.after', ['order' => $order]) !!}

                            <div class="row">
                                <span class="title">
                                    {{ __('admin::app.sales.orders.order-status') }}
                                </span>

                                <span id="data-order_status" class="value">
                                    {{ $order->status_label }}
                                </span>
                            </div>

                            {!! view_render_event('sales.order.status_label.after', ['order' => $order]) !!}

                            <div class="row">
                                <span class="title">
                                    {{ __('admin::app.sales.orders.channel') }}
                                </span>

                                <span id="data-order_channel_name" class="value">
                                    {{ $order->channel_name }}
                                </span>
                            </div>

                            {!! view_render_event('sales.order.channel_name.after', ['order' => $order]) !!}
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
                                    {{ $order->customer_full_name }}
                                </span>
                            </div>

                            {!! view_render_event('sales.order.customer_full_name.after', ['order' => $order])
                            !!}

                            <div class="row">
                                <span class="title">
                                    {{ __('admin::app.sales.orders.email') }}
                                </span>

                                <span id="data-customer_email" class="value">
                                    {{ $order->customer_email }}
                                </span>
                            </div>

                            {!! view_render_event('sales.order.customer_email.after', ['order' => $order]) !!}

                            @if (! is_null($order->customer) && ! is_null($order->customer->group))
                            <div class="row">
                                <span class="title">
                                    {{ __('admin::app.customers.customers.customer_group') }}
                                </span>

                                <span class="value">
                                    {{ $order->customer->group->name }}
                                </span>
                            </div>
                            @endif

                            {!! view_render_event('sales.order.customer_group.after', ['order' => $order]) !!}
                        </div>
                    </div>

                </div>
            </accordian>

            @if ($order->billing_address || $order->shipping_address)
            <accordian :title="'{{ __('admin::app.sales.orders.address') }}'" :active="true">
                <div slot="body">

                    @if($order->billing_address)
                    <div class="sale-section">
                        <div class="secton-title">
                            <span>{{ __('admin::app.sales.orders.billing-address') }}</span>
                        </div>

                        <div id="data-billing_address" class="section-content">
                            @include ('admin::sales.address', ['address' => $order->billing_address])

                            {!! view_render_event('sales.order.billing_address.after', ['order' => $order]) !!}
                        </div>
                    </div>
                    @endif

                    @if ($order->shipping_address)
                    <div class="sale-section">
                        <div class="secton-title">
                            <span>{{ __('admin::app.sales.orders.shipping-address') }}</span>
                        </div>

                        <div id="data-shipping_address" class="section-content">
                            @include ('admin::sales.address', ['address' => $order->shipping_address])

                            {!! view_render_event('sales.order.shipping_address.after', ['order' => $order]) !!}
                        </div>
                    </div>
                    @endif

                </div>
            </accordian>
            @endif

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
                                    {{ core()->getConfigData('sales.paymentmethods.' . $order->payment->method .
                                            '.title') }}
                                </span>
                            </div>

                            <div class="row">
                                <span class="title">
                                    {{ __('admin::app.sales.orders.currency') }}
                                </span>

                                <span id="data-order_currency" class="value">
                                    {{ $order->order_currency_code }}
                                </span>
                            </div>

                            @php $additionalDetails =
                            \Webkul\Payment\Payment::getAdditionalDetails($order->payment->method); @endphp

                            @if (! empty($additionalDetails))
                            <div class="row">
                                <span class="title">
                                    {{ $additionalDetails['title'] }}
                                </span>

                                <span class="value">
                                    {{ $additionalDetails['value'] }}
                                </span>
                            </div>
                            @endif

                            {!! view_render_event('sales.order.payment-method.after', ['order' => $order]) !!}
                        </div>
                    </div>

                    @if ($order->shipping_address)
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
                                    {{ $order->shipping_title }}
                                </span>
                            </div>

                            <div class="row">
                                <span class="title">
                                    {{ __('admin::app.sales.orders.shipping-price') }}
                                </span>

                                <span id="data-shipping_price" class="value">
                                    {{ core()->formatBasePrice($order->base_shipping_amount) }}
                                </span>
                            </div>

                            {!! view_render_event('sales.order.shipping-method.after', ['order' => $order]) !!}
                        </div>
                    </div>
                    @endif
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
                                    @if ($order->base_discount_amount > 0)
                                    <th>{{ __('admin::app.sales.orders.discount-amount') }}</th>
                                    @endif
                                    <th>{{ __('admin::app.sales.orders.grand-total') }}</th>
                                </tr>
                            </thead>

                            <tbody>

                                @foreach ($order->items as $item)

                                <tr>
                                    <td>
                                        {{ $item->getTypeInstance()->getOrderedItem($item)->sku }}
                                    </td>

                                    <td>
                                        {{ $item->name }}

                                        @if (isset($item->additional['attributes']))
                                        <div class="item-options">

                                            @foreach ($item->additional['attributes'] as $attribute)
                                            <b>{{ $attribute['attribute_name'] }} : </b>{{
                                                    $attribute['option_label'] }}</br>
                                            @endforeach

                                        </div>
                                        @endif
                                    </td>

                                    <td>{{ core()->formatBasePrice($item->base_price) }}</td>

                                    <td>
                                        <span class="qty-row">
                                            {{ $item->qty_ordered ? __('admin::app.sales.orders.item-ordered',
                                                    ['qty_ordered' => $item->qty_ordered]) : '' }}
                                        </span>

                                        <span class="qty-row">
                                            {{ $item->qty_invoiced ? __('admin::app.sales.orders.item-invoice',
                                                    ['qty_invoiced' => $item->qty_invoiced]) : '' }}
                                        </span>

                                        <span class="qty-row">
                                            {{ $item->qty_shipped ? __('admin::app.sales.orders.item-shipped',
                                                    ['qty_shipped' => $item->qty_shipped]) : '' }}
                                        </span>

                                        <span class="qty-row">
                                            {{ $item->qty_refunded ? __('admin::app.sales.orders.item-refunded',
                                                    ['qty_refunded' => $item->qty_refunded]) : '' }}
                                        </span>

                                        <span class="qty-row">
                                            {{ $item->qty_canceled ? __('admin::app.sales.orders.item-canceled',
                                                    ['qty_canceled' => $item->qty_canceled]) : '' }}
                                        </span>
                                    </td>

                                    <td>{{ core()->formatBasePrice($item->base_total) }}</td>

                                    <td>{{ $item->tax_percent }}%</td>

                                    <td>{{ core()->formatBasePrice($item->base_tax_amount) }}</td>

                                    @if ($order->base_discount_amount > 0)
                                    <td>{{ core()->formatBasePrice($item->base_discount_amount) }}</td>
                                    @endif

                                    <td>{{ core()->formatBasePrice($item->base_total + $item->base_tax_amount -
                                                $item->base_discount_amount) }}</td>
                                </tr>
                                @endforeach
                        </table>
                    </div>

                    <div class="summary-comment-container">
                        <div class="comment-container">
                            <form action="{{ route('admin.sales.orders.comment', $order->id) }}" method="post" @submit.prevent="onSubmit">
                                @csrf()

                                <div class="control-group" :class="[errors.has('comment') ? 'has-error' : '']">
                                    <label for="comment" class="required">{{
                                                __('admin::app.sales.orders.comment') }}</label>
                                    <textarea v-validate="'required'" class="control" id="comment" name="comment" data-vv-as="&quot;{{ __('admin::app.sales.orders.comment') }}&quot;"></textarea>
                                    <span class="control-error" v-if="errors.has('comment')">@{{
                                                errors.first('comment') }}</span>
                                </div>

                                <div class="control-group">
                                    <span class="checkbox">
                                        <input type="checkbox" name="customer_notified" id="customer-notified" name="checkbox[]">
                                        <label class="checkbox-view" for="customer-notified"></label>
                                        {{ __('admin::app.sales.orders.notify-customer') }}
                                    </span>
                                </div>

                                <button type="submit" class="btn btn-lg btn-primary">
                                    {{ __('admin::app.sales.orders.submit-comment') }}
                                </button>
                            </form>

                            <ul class="comment-list">
                                @foreach ($order->comments()->orderBy('id', 'desc')->get() as $comment)
                                <li>
                                    <span class="comment-info">
                                        @if ($comment->customer_notified)
                                        {!! __('admin::app.sales.orders.customer-notified', ['date' =>
                                        $comment->created_at]) !!}
                                        @else
                                        {!! __('admin::app.sales.orders.customer-not-notified', ['date' =>
                                        $comment->created_at]) !!}
                                        @endif
                                    </span>

                                    <p>{{ $comment->comment }}</p>
                                </li>
                                @endforeach
                            </ul>
                        </div>

                        <table class="sale-summary">
                            <tr>
                                <td>{{ __('admin::app.sales.orders.subtotal') }}</td>
                                <td>-</td>
                                <td>{{ core()->formatBasePrice($order->base_sub_total) }}</td>
                            </tr>

                            @if ($order->haveStockableItems())
                            <tr>
                                <td>{{ __('admin::app.sales.orders.shipping-handling') }}</td>
                                <td>-</td>
                                <td>{{ core()->formatBasePrice($order->base_shipping_amount) }}</td>
                            </tr>
                            @endif

                            @if ($order->base_discount_amount > 0)
                            <tr>
                                <td>
                                    {{ __('admin::app.sales.orders.discount') }}

                                    @if ($order->coupon_code)
                                    ({{ $order->coupon_code }})
                                    @endif
                                </td>
                                <td>-</td>
                                <td>{{ core()->formatBasePrice($order->base_discount_amount) }}</td>
                            </tr>
                            @endif

                            <tr class="border">
                                <td>{{ __('admin::app.sales.orders.tax') }}</td>
                                <td>-</td>
                                <td>{{ core()->formatBasePrice($order->base_tax_amount) }}</td>
                            </tr>

                            <tr class="bold">
                                <td>{{ __('admin::app.sales.orders.grand-total') }}</td>
                                <td>-</td>
                                <td>{{ core()->formatBasePrice($order->base_grand_total) }}</td>
                            </tr>

                            <tr class="bold">
                                <td>{{ __('admin::app.sales.orders.total-paid') }}</td>
                                <td>-</td>
                                <td>{{ core()->formatBasePrice($order->base_grand_total_invoiced) }}</td>
                            </tr>

                            <tr class="bold">
                                <td>{{ __('admin::app.sales.orders.total-refunded') }}</td>
                                <td>-</td>
                                <td>{{ core()->formatBasePrice($order->base_grand_total_refunded) }}</td>
                            </tr>

                            <tr class="bold">
                                <td>{{ __('admin::app.sales.orders.total-due') }}</td>

                                <td>-</td>

                                @if($order->status !== 'canceled')
                                <td>{{ core()->formatBasePrice($order->base_total_due) }}</td>
                                @else
                                <td id="due-amount-on-cancelled">{{ core()->formatBasePrice(0.00) }}</td>
                                @endif
                            </tr>
                        </table>
                    </div>
                </div>
            </accordian>
        </div>
        <!-- Modal Footer -->
        <div class="cashier-modal-footer">
            <button type="submit" class="btn btn-lg btn-primary" style="background-color: orange;">Cancel</button>
            <button type="submit" class="btn btn-lg btn-primary" style="background-color: orange;">Refund</button>
            <button type="submit" class="btn btn-lg btn-primary" style="background-color: orange;">Invoice</button>
            <button type="submit" class="btn btn-lg btn-primary" style="background-color: orange;">Deliver</button>
            <button type="submit" class="btn btn-lg btn-primary" style="background-color: green;">Print</button>
        </div>
    </div>

</div>
@stop