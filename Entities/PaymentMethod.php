<?php

namespace Modules\Icommerce\Entities;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Modules\Media\Support\Traits\MediaRelation;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;
use Modules\Iforms\Support\Traits\Formeable;

class PaymentMethod extends Model
{
  use Translatable, MediaRelation, Formeable, BelongsToTenant;
  
  public $translatedAttributes = [
    'title',
    'description'
  ];
  
  protected $table = 'icommerce__payment_methods';
  
  protected $fillable = [
    'status',
    'name',
    'options',
    'store_id',
    'payout',
    'geozone_id',
    'parent_name'
  ];
  

  protected $casts = [
    'options' => 'array'
  ];
  
  public $tenantWithCentralData = false;
  
  public function __construct(array $attributes = [])
  {
    try{
      if (!\App::runningInConsole()) {
        $entitiesWithCentralData = json_decode(setting("icommerce::tenantWithCentralData", null, "[]"));
        $this->tenantWithCentralData = in_array("paymentMethods", $entitiesWithCentralData);
      }
    }catch(\Exception $e){}
    
    parent::__construct($attributes);
  }
  
  public function store()
  {
    if (is_module_enabled('Marketplace')) {
      return $this->belongsTo('Modules\Marketplace\Entities\Store');
    }
    return $this->belongsTo(Store::class);
  }
  
  public function getOptionsAttribute($value)
  {
    return json_decode($value);
  }
  
  
  public function transactions()
  {
    return $this->hasMany(Transaction::class);
  }
  
  public function geozones()
  {
    return $this->belongsToMany('Modules\Ilocations\Entities\Geozones', 'icommerce__payment_methods_geozones', 'payment_method_id', 'geozone_id')->withTimestamps();
  }
  
  public function getMainImageAttribute()
  {
    $thumbnail = $this->files()->where('zone', 'mainimage')->first();
    if (!$thumbnail) {
      if (isset($this->options->mainimage)) {
        $image = [
          'mimeType' => 'image/jpeg',
          'path' => url($this->options->mainimage)
        ];
      } else {
        $image = [
          'mimeType' => 'image/jpeg',
          'path' => url('modules/iblog/img/post/default.jpg')
        ];
      }
    } else {
      $image = [
        'mimeType' => $thumbnail->mimetype,
        'path' => $thumbnail->path_string
      ];
    }
    return json_decode(json_encode($image));
  }
}
