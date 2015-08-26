<?php namespace Abnmt\TheaterPress\Models;

use Model;
use Str;

/**
 * Article Model
 */
class Article extends Model
{

    /**
     * @var string The database table used by the model.
     */
    public $table = 'abnmt_theaterpress_articles';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [];

    /**
     * The attributes that should be mutated to dates.
     * @var array
     */
    protected $dates = ['published_at'];

    /**
     * The attributes on which the post list can be ordered
     * @var array
     */
    public static $allowedSortingOptions = array(
        'title asc'         => 'По заголовку (asc)',
        'title desc'        => 'По заголовку (desc)',
        'published_at asc'  => 'По дате публикации (asc)',
        'published_at desc' => 'По дате публикации (desc)',
    );

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [];
    public $belongsTo = [];
    public $belongsToMany = [
        'categories' => ['Abnmt\TheaterPress\Models\Category', 'table' => 'abnmt_theaterpress_articles_categories', 'order' => 'name']
    ];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [
        'cover' => ['System\Models\File']
    ];
    public $attachMany = [];



    public function beforeCreate()
    {
        // Generate a URL slug for this model
        $this->slug = Str::slug($this->title);
    }

    //
    // Scopes
    //

    /**
     * The attributes of articles Scopes
     * @var array
     */
    public static $allowedScopingOptions = [
        'getPressFeed' => 'Новостная лента',
    ];

    public function scopeIsPublished($query)
    {
        return $query
            ->whereNotNull('published')
            ->where('published', true)
        ;
    }

    public function scopeGetPressFeed($query)
    {
        $query
            ->isPublished()
            ->with(['cover'])
            ->orderBy('published_at', 'desc')
            ->take(6)
        ;

        return $query->get();
    }


    /**
     * Lists articles for the front end
     * @param  array $options Display options
     * @return self
     */
    public function scopeListFrontEnd($query, $options)
    {
        /*
         * Default options
         */
        extract(array_merge([
            'page'       => 1,
            'perPage'    => 10,
            'sort'       => 'published_at',
            'categories' => null,
            'search'     => '',
            'published'  => true
        ], $options));

        $searchableFields = ['title', 'slug', 'excerpt', 'content'];

        if ($published)
            $query->isPublished();

        /*
         * Sorting
         */
        if (!is_array($sort)) $sort = [$sort];
        foreach ($sort as $_sort) {

            if (in_array($_sort, array_keys(self::$allowedSortingOptions))) {
                $parts = explode(' ', $_sort);
                if (count($parts) < 2) array_push($parts, 'desc');
                list($sortField, $sortDirection) = $parts;

                $query->orderBy($sortField, $sortDirection);
            }
        }

        /*
         * Search
         */
        $search = trim($search);
        if (strlen($search)) {
            $query->searchWhere($search, $searchableFields);
        }

        /*
         * Categories
         */
        if ($categories !== null) {
            if (!is_array($categories)) $categories = [$categories];
            $query->whereHas('categories', function($q) use ($categories) {
                $q->whereIn('id', $categories);
            });
        }

        return $query->paginate($perPage, $page);
    }

    /**
     * Sets the "url" attribute with a URL to this object
     * @param string $pageName
     * @param Cms\Classes\Controller $controller
     */
    public function setUrl($pageName, $controller)
    {
        $params = [
            'id' => $this->id,
            'slug' => $this->slug,
        ];

        if (array_key_exists('categories', $this->getRelations())) {
            $params['category'] = $this->categories->count() ? $this->categories->first()->slug : null;
        }

        return $this->url = $controller->pageUrl($pageName, $params);
    }

}