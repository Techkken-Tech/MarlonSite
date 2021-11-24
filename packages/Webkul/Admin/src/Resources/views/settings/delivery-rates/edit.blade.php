@extends('admin::layouts.content')

@section('page_title')
    {{ __('admin::app.settings.delivery-rates.edit-title') }}
@stop

@section('content')
    <div class="content">

        <form method="POST" action="{{ route('admin.delivery-rates.update', $deliveryRate->id) }}" @submit.prevent="onSubmit" enctype="multipart/form-data">
            <div class="page-header">
                <div class="page-title">
                    <h1>
                        <i class="icon angle-left-icon back-link" onclick="window.location = '{{ route('admin.delivery-rates.index') }}'"></i>

                        {{ __('admin::app.settings.delivery-rates.edit-title') }}
                    </h1>
                </div>

                <div class="page-action">
                    <button type="submit" class="btn btn-lg btn-primary">
                        {{ __('admin::app.settings.delivery-rates.save-btn-title') }}
                    </button>
                </div>
            </div>

            <div class="page-content">
                <div class="form-container">
                    @csrf()

                    {!! view_render_event('bagisto.admin.settings.delivery-rates.edit.before', ['deliveryRate' => $deliveryRate]) !!}

                    <input name="_method" type="hidden" value="PUT">

                    <accordian :title="'{{ __('admin::app.settings.delivery-rates.general') }}'" :active="true">
                        <div slot="body">

                            <div class="control-group" :class="[errors.has('name') ? 'has-error' : '']">
                                <label for="name" class="required">{{ __('admin::app.settings.delivery-rates.name') }}</label>
                                <input type="text" v-validate="'required'" class="control" id="name" name="name" data-vv-as="&quot;{{ __('admin::app.settings.delivery-rates.name') }}&quot;" value="{{ old('name') ?: $deliveryRate->name }}" disabled="disabled"/>
                                <input type="hidden" name="name" value="{{ $deliveryRate->name }}"/>
                                <span class="control-error" v-if="errors.has('name')">@{{ errors.first('name') }}</span>
                            </div>

                            <div class="control-group" :class="[errors.has('estimated_time') ? 'has-error' : '']">
                                <label for="estimated_time" class="required">{{ __('admin::app.settings.delivery-rates.estimated_time') }}</label>
                                <input v-validate="'required'" class="control" id="estimated_time" name="estimated_time" data-vv-as="&quot;{{ __('admin::app.settings.delivery-rates.estimated_time') }}&quot;" value="{{ old('estimated_time') ?: $deliveryRate->estimated_time }}"/>
                                <span class="control-error" v-if="errors.has('estimated_time')">@{{ errors.first('estimated_time') }}</span>
                            </div>

                            <div class="control-group" :class="[errors.has('rate') ? 'has-error' : '']">
                                <label for="rate" class="required">{{ __('admin::app.settings.delivery-rates.rate') }}</label>
                                <input v-validate="'required'" class="control" id="rate" name="rate" data-vv-as="&quot;{{ __('admin::app.settings.delivery-rates.rate') }}&quot;" value="{{ old('rate') ?: $deliveryRate->rate }}"/>
                                <span class="control-error" v-if="errors.has('rate')">@{{ errors.first('rate') }}</span>
                            </div>


                        </div>
                    </accordian>

                    {!! view_render_event('bagisto.admin.settings.delivery-rates.edit.after', ['deliveryRate' => $deliveryRate]) !!}
                </div>
            </div>
        </form>
    </div>
@stop