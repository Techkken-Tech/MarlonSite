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