<?php namespace Abnmt\TheaterPress\Components;

use Cms\Classes\ComponentBase;
use Cms\Classes\Page;

use Abnmt\TheaterPress\Models\Post     as PostModel;
use Abnmt\TheaterPress\Models\Category as CategoryModel;

use CW;

class Archive extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name'        => 'abnmt.theaterpress::lang.components.archive.name',
            'description' => 'abnmt.theaterpress::lang.components.archive.description'
        ];
    }

    /**
     * A collection of posts to display
     * @var Collection
     */
    public $posts;

    /**
     * Parameter to use for the page number
     * @var string
     */
    public $pageParam;

    /**
     * If the post list should be filtered by a category, the model to use.
     * @var Model
     */
    public $category;

    /**
     * Reference to the page name for linking to posts.
     * @var string
     */
    public $postPage;

    /**
     * Reference to the page name for linking to categories.
     * @var string
     */
    public $categoryPage;

    public function defineProperties()
    {
        return [
            'pageNumber' => [
                'title'       => 'abnmt.theaterpress::lang.settings.posts_pagination',
                'description' => 'abnmt.theaterpress::lang.settings.posts_pagination_description',
                'type'        => 'string',
                'default'     => '{{ :page }}',
            ],
            'categoryFilter' => [
                'title'       => 'abnmt.theaterpress::lang.settings.posts_filter',
                'description' => 'abnmt.theaterpress::lang.settings.posts_filter_description',
                'type'        => 'string',
                'default'     => ''
            ],
            'postsPerPage' => [
                'title'             => 'abnmt.theaterpress::lang.settings.posts_per_page',
                'type'              => 'string',
                'validationPattern' => '^[0-9]+$',
                'validationMessage' => 'abnmt.theaterpress::lang.settings.posts_per_page_validation',
                'default'           => '10',
            ],
            'categoryPage' => [
                'title'       => 'abnmt.theaterpress::lang.settings.posts_category',
                'description' => 'abnmt.theaterpress::lang.settings.posts_category_description',
                'type'        => 'dropdown',
                'default'     => 'theaterPress/category',
                'group'       => 'Страницы',
            ],
            'postPage' => [
                'title'       => 'abnmt.theaterpress::lang.settings.posts_post',
                'description' => 'abnmt.theaterpress::lang.settings.posts_post_description',
                'type'        => 'dropdown',
                'default'     => 'theaterPress/post',
                'group'       => 'Страницы',
            ],
        ];
    }

    public function getCategoryPageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }
    public function getPostPageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }


    public function onRun()
    {
        $this->prepareVars();

        $this->category = $this->page['category'] = $this->loadCategory();
        $this->posts    = $this->page['posts']    = $this->listPosts();

        /*
         * If the page number is not valid, redirect
         */
        if ($pageNumberParam = $this->paramName('pageNumber')) {
            $currentPage = $this->property('pageNumber');

            if ($currentPage > ($lastPage = $this->posts->lastPage()) && $currentPage > 1)
                return Redirect::to($this->currentPageUrl([$pageNumberParam => $lastPage]));
        }
    }

    protected function prepareVars()
    {
        $this->pageParam      = $this->page['pageParam']      = $this->paramName('pageNumber');

        /*
         * Page links
         */
        $this->postPage     = $this->page['postPage']     = $this->property('postPage');
        $this->categoryPage = $this->page['categoryPage'] = $this->property('categoryPage');
    }

    protected function listPosts()
    {
        $categories = $this->category ? $this->category->id : null;

        /*
         * List all the posts, eager load their categories
         */
        $posts = PostModel::with('categories')->listFrontEnd([
            'page'       => $this->property('pageNumber'),
            'perPage'    => $this->property('postsPerPage'),
            'categories' => $categories
        ]);

        /*
         * Add a "url" helper attribute for linking to each post and category
         */
        $posts->each(function($post){
            $post->setUrl($this->postPage, $this->controller);

            $post->categories->each(function($category){
                $category->setUrl($this->categoryPage, $this->controller);
            });
        });

        CW::info(['PressArchive' => $posts]);

        return $posts;
    }

    protected function loadCategory()
    {
        if (!$categoryId = $this->property('categoryFilter'))
            return null;

        if (!$category = CategoryModel::whereSlug($categoryId)->first())
            return null;

        return $category;
    }

}