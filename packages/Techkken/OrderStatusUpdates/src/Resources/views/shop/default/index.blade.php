@extends('shop::layouts.master')

@section('page_title')
    Order Status
@stop

@section('head')
<script src="//js.pusher.com/3.1/pusher.min.js"></script>
        <script>


            var pusher = new Pusher('ece51902004461ed7181', {
            cluster: 'ap1',
            encrypted: true
            });


            // Subscribe to the channel we specified in our Laravel Event
            var channel = pusher.subscribe('order{{$order->id}}');
            channel.bind('pusher:subscription_succeeded', function(members) {
                 console.log('successfully subscribed!');
            });
            // Bind a function to a Event (the full Laravel class)
            channel.bind('order{{$order->id}}', function(data) {
                location.reload();
            });

</script>
@stop

@section('content-wrapper')

    <div class="main order-status">
       <h1> {{ __('shop::app.customer.account.order.view.page-tile', ['order_id' => $order->increment_id]) }}</h1>
        <?php 
            if ($order->status == 'pending') {
            
            ?>
            <p>{{__('Your order is now being prepared.')}}</p>
            <div class='pending-animation'><img class="img-fluid" src="https://i.gifer.com/3R7s.gif" /></div>
        <?php
            }
        ?>
        <?php 
            if ($order->status == 'completed') {
            
            ?>
            <p>{{__('This order is delivered.')}}<a href='/menu'> &nbsp; {{__('Click here to order again.')}}</a></p>
            <div class='pending-animation'><img src="https://i.gifer.com/7efs.gif" /></div>
        <?php
            }
        ?>
        <?php 
            if ($order->status == 'processing') {
            
            ?>
            <p>{{__('Please wait for your order')}}</p>
            <div class='pending-animation'><img src="{{ bagisto_asset('images/delivering.gif') }}" /></div>
        <?php
            }
        ?>
    </div>

@stop
