<?php

namespace App;

use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $isbn
 * @property string $title
 * @property string $description
 * @property string $published_year
 */
class Book extends Model
{
    public $timestamps = false;
    use Searchable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'books';
    protected $primaryKey = 'id';
    public $incrementing = false;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'isbn',
        'title',
        'description',
        'published_year'
    ];
    public $searchable = ['isbn','tittle','description', 'published_year'];

  public $appends = [
      'book_author','review'
    ];

    public function getBookAuthorAttribute(){
        return $this->belongsTo(BookAuthor::class,
            'id',
            'book_id')->first();
    }

    public function getReviewAttribute(){
        return $this->belongsTo(BookReview::class,
            'id',
            'id')->first();
    }

}
