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
            });
            // Bind a function to a Event (the full Laravel class)
            channel.bind('order{{$order->id}}', function(data) {
                if(data != null){
                    if(data.status == "completed"){
                        $('.order-status-text').html(
                            "{{__('This order is delivered.')}}<a href='/menu'> &nbsp; {{__('Click here to order again.')}}</a>"
                        );
                        $('.pending-animation img').attr("src","https://i.gifer.com/7efs.gif");

                    }
                    if(data.status == "processing"){
                        $('.order-status-text').html(
                            "{{__('Please wait for your order')}}"
                        );
                        $('.pending-animation img').attr("src","{{ bagisto_asset('images/delivering.gif') }}");

                    }
                }
            });

</script>
@stop

@section('content-wrapper')

    <div class="main order-status">
       <h1> {{ __('shop::app.customer.account.order.view.page-tile', ['order_id' => $order->increment_id]) }}</h1>
        <?php 
            if ($order->status == 'pending') {
            
            ?>
            <p class="order-status-text">{{__('Your order is now being prepared.')}}</p>
            <div class='pending-animation'><img class="img-fluid" src="https://i.gifer.com/3R7s.gif" /></div>
        <?php
            }
        ?>
        <?php 
            if ($order->status == 'completed') {
            
            ?>
            <p  class="order-status-text">{{__('This order is delivered.')}}<a href='/menu'> &nbsp; {{__('Click here to order again.')}}</a></p>
            <div class='pending-animation'><img src="https://i.gifer.com/7efs.gif" /></div>
        <?php
            }
        ?>
        <?php 
            if ($order->status == 'processing') {
            
            ?>
            <p  class="order-status-text">{{__('Please wait for your order')}}</p>
            <div class='pending-animation'><img src="{{ bagisto_asset('images/delivering.gif') }}" /></div>
        <?php
            }
        ?>
    </div>

@stop
