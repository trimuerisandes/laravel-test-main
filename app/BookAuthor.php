<?php

namespace App;

use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;

class BookAuthor extends Model
{
    use Searchable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'book_author';
    protected $primaryKey = 'book_id';
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['book_id','author_id'];

    /**
     * The attributes that are searchable.
     *
     * @var array
     */
    public $searchable = ['book_id','author_id'];


    public $appends = [
        'authors'
    ];

    public function getAuthorsAttribute(){
        return $this->belongsTo(Author::class,
            'author_id',
            'id')->first();

    }
}
