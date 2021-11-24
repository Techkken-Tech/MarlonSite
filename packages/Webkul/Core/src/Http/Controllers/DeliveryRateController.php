<?php

namespace Webkul\Core\Http\Controllers;

use Illuminate\Support\Facades\Event;
use Webkul\Core\Repositories\DeliveryRateRepository;

class DeliveryRateController extends Controller
{
    /**
     * Contains route related configuration
     *
     * @var array
     */
    protected $_config;

    /**
     * DeliveryRateRepository object
     *
     * @var \Webkul\Core\Repositories\DeliveryRateRepository
     */
    protected $deliveryRepository;

    /**
     * Create a new controller instance.
     *
     * @param  \Webkul\Core\Repositories\DeliveryRateRepository  $deliveryRateRepository
     * @return void
     */
    public function __construct(DeliveryRateRepository $deliveryRateRepository)
    {
        $this->deliveryRateRepository = $deliveryRateRepository;

        $this->_config = request('_config');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view($this->_config['view']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view($this->_config['view']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $this->validate(request(), [
            'name'              => 'required',
            'estimated_time'    => 'string',
            'rate'              => 'required|numeric',
        ]);

        Event::dispatch('core.delivery-rates.create.before');

        $deliveryRate = $this->deliveryRateRepository->create(request()->all());

        Event::dispatch('core.delivery-rates.create.after', $deliveryRate);

        session()->flash('success', trans('admin::app.settings.delivery-rates.create-success'));

        return redirect()->route($this->_config['redirect']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $deliveryRate = $this->deliveryRateRepository->findOrFail($id);

        return view($this->_config['view'], compact('deliveryRate'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        $this->validate(request(), [
            'name'              => 'required',
            'estimated_time'    => 'string',
            'rate'              => 'required|numeric',
        ]);

        Event::dispatch('core.delivery-rates.update.before', $id);

        $deliveryRate = $this->deliveryRateRepository->update(request()->all(), $id);

        Event::dispatch('core.delivery-rates.update.after', $deliveryRate);

        session()->flash('success', trans('admin::app.settings.delivery-rates.update-success'));

        return redirect()->route($this->_config['redirect']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $deliveryRate = $this->deliveryRateRepository->findOrFail($id);

        if ($this->deliveryRateRepository->count() == 1) {
            session()->flash('warning', trans('admin::app.settings.delivery-rates.last-delete-error'));
        } else {
            try {
                Event::dispatch('core.delivery_rates.delete.before', $id);

                $this->deliveryRateRepository->delete($id);

                Event::dispatch('core.delivery_rates.delete.after', $id);

                session()->flash('success', trans('admin::app.settings.delivery-rates.delete-success'));

                return response()->json(['message' => true], 200);
            } catch(\Exception $e) {
                session()->flash('error', trans('admin::app.response.delete-failed', ['name' => 'Delivery Rate']));
            }
        }

        return response()->json(['message' => false], 400);
    }


    public function viewDeliveryRates()
    {
        $deliveryRate = $this->deliveryRateRepository->all();
        return $deliveryRate;
    }
}