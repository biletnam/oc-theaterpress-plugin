<?php namespace Abnmt\TheaterPress\Components;

use Cms\Classes\ComponentBase;
use Cms\Classes\Page;

use Abnmt\TheaterPress\Models\Post as PostModel;

use CW;

class Press extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name'        => 'abnmt.theaterpress::lang.components.press.name',
            'description' => 'abnmt.theaterpress::lang.components.press.description'
        ];
    }

    /**
     * @var The post model used for display.
     */
    public $post;

    /**
     * @var string Reference to the page name for linking to categories.
     */
    public $categoryPage;


    public function defineProperties()
    {
        return [
            'slug' => [
                'title'       => 'abnmt.theaterpress::lang.settings.post_slug',
                'description' => 'abnmt.theaterpress::lang.settings.post_slug_description',
                'default'     => '{{ :slug }}',
                'type'        => 'string'
            ],
            'categoryPage' => [
                'title'       => 'abnmt.theaterpress::lang.settings.post_category',
                'description' => 'abnmt.theaterpress::lang.settings.post_category_description',
                'type'        => 'dropdown',
                'default'     => 'theaterPress/category',
            ],
        ];
    }

    public function getCategoryPageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    public function onRun()
    {
        $this->categoryPage = $this->page['categoryPage'] = $this->property('categoryPage');
        $this->post = $this->page['post'] = $this->loadPost();
    }

    protected function loadPost()
    {
        $slug = $this->property('slug');
        $post = PostModel::isPublished()->where('slug', $slug)->first();

        /*
         * Add a "url" helper attribute for linking to each category
         */
        if ($post && $post->categories->count()) {
            $post->categories->each(function($category){
                $category->setUrl($this->categoryPage, $this->controller);
            });
        }

        CW::info(['Press' => $post]);

        return $post;
    }

}