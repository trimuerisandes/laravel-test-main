<?php

namespace App;

use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;


/**
 * @property integer $id
 * @property string $name
 * @property string $surname
 */
class Author extends Model
{
    public $timestamps = false;
    use Searchable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'authors';
    protected $primaryKey = 'id';
    public $incrementing = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'surname',
    ];

    /**
     * The attributes that are searchable.
     *
     * @var array
     */
    public $searchable = ['name','surname'];

}
