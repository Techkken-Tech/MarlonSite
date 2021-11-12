@extends('techkken::layouts.content')

@section('page_title')
{{ __('techkken::app.techkken.cashier.title') }}
@stop

@section('content')
<section class="cashier-orders">
    <h3 class="cashier-title" style="margin: 5px 10px 5px 20px;">Cashier</h3>
    <div class="cashier-order-list" style="margin-top: 20px;">
        <div class="cashier-row-card" style="border: 2px solid #000000;">
            <p>This is the first CARD</p>
        </div>
    </div>
</section>
@stop