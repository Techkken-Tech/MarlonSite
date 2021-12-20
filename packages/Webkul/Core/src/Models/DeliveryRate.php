<?php

namespace Webkul\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Core\Contracts\DeliveryRate as ContractsDeliveryRate;

class DeliveryRate extends Model implements ContractsDeliveryRate
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'estimated_time',
        'rate',
        'minimum_cartvalue'
    ];
}
