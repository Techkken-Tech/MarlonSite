@extends('techkken::layouts.content')

@section('page_title')
{{ __('techkken::app.techkken.cashier.title') }}
@stop

@section('content')
<section class="cashier-orders">
    <div class="cashier-header">
        <button onclick="toggleNavBarLeft();"><img class="bars-icon"/></button>
        <h1 class="cashier-title">Cashier</h1>
    </div>

    <form class="form-inline cashier-search" action="">
        <label for="searching">Filter:</label>
        <input type="text" placeholder="Enter Order Number" id="order_number" name="order_number">
        <input type="text" placeholder="Enter search keyword" id="keyword">
        <select>
            <option value="Pending">Pending</option>
        </select>
        <input type="date" placeholder="Start Date" id="start_date" name="start_date">
        <input type="date" placeholder="End Date" id="end_date" name="end_date">
        <button type="button">Search</button>
    </form>


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
                <div><span class="badge badge-md badge-success">Completed</span></div>
            </div>
            <div class="cashier-row-total">
                P10000
            </div>
            <div class="cashier-row-action">
                <div><span class="icon eye-icon"></span></div>
            </div>
        </div>
        @endforeach

    </div>
</section>
@stop