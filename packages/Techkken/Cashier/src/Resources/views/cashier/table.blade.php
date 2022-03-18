@foreach ($orders as $order)
<div class="cashier-row-card">
    <div class="cashier-row-order-no">
        <div class="cashier-order-no">#<span>{{$order->increment_id}}</span></div>
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
            @case("processing")
            <span class="badge badge-md badge-danger">{{__('Delivering')}}</span>
            @break

            @default

            @endswitch
        </div>
    </div>
    <div class="cashier-row-total">
        PHP. {{number_format($order->base_grand_total_invoiced,2)}}
    </div>
    <div class="cashier-row-action">
        <div style="display:inline-block" class="action-view" onclick="viewOrder('{{ route('cashier.viewOrder', [$order->id]) }}');"><span class="btn btn-md btn-black">VIEW</span></div>
        &nbsp;
        <a class="action-process" href="{{ route('cashier.processOrder', [$order->id]) }}"><span class="btn btn-md btn-black">

                @switch($order->status)
                @case("pending")
                DELIVER
                @break

                @case("processing")
                DONE
                @break

                @default

                @endswitch


            </span></a>
    </div>
</div>
@endforeach
