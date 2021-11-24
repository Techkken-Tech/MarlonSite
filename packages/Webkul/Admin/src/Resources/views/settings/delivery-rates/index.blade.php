@extends('admin::layouts.content')

@section('page_title')
    {{ __('admin::app.settings.delivery-rates.title') }}
@stop

@section('content')
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h1>{{ __('admin::app.settings.delivery-rates.title') }}</h1>
            </div>

            <div class="page-action">
                <a href="{{ route('admin.delivery-rates.create') }}" class="btn btn-lg btn-primary">
                    {{ __('admin::app.settings.delivery-rates.add-title') }}
                </a>
            </div>
        </div>

        <div class="page-content">

            @inject('delivery_rates','Webkul\Admin\DataGrids\DeliveryRatesDataGrid')
            {!! $delivery_rates->render() !!}
        </div>
    </div>
@stop