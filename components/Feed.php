<?php namespace Abnmt\TheaterPress\Components;

use Cms\Classes\ComponentBase;
use Cms\Classes\Page;

use Abnmt\TheaterPress\Models\Post as PostModel;

use CW;

class Feed extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name'        => 'abnmt.theaterpress::lang.components.feed.name',
            'description' => 'abnmt.theaterpress::lang.components.feed.description'
        ];
    }

    public function defineProperties()
    {
        return [
            'postPage' => [
                'title'       => 'Страница новости',
                'description' => 'CMS страница для вывода новости',
                'type'        => 'dropdown',
                'default'     => 'theaterPress/post',
                'group'       => 'Страницы',
            ],
            'categoryPage' => [
                'title'       => 'Страница категории новостей',
                'description' => 'CMS страница для вывода новостей одной категории',
                'type'        => 'dropdown',
                'default'     => 'theaterPress/category',
                'group'       => 'Страницы',
            ],
            'archivePage' => [
                'title'       => 'Страница архива новостей',
                'description' => 'CMS страница для вывода архива новостей',
                'type'        => 'dropdown',
                'default'     => 'theaterPress/archive',
                'group'       => 'Страницы',
            ],
        ];
    }

    public function getPostPageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }
    public function getCategoryPageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }
    public function getArchivePageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }


    /**
     * A collection of posts to display
     * @var Collection
     */
    public $posts;


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

    /**
     * Reference to the page name for linking to categories.
     * @var string
     */
    public $archivePage;


    /**
     *  onRun function
     */
    public function onRun()
    {
        $this->prepareVars();

        $this->posts = $this->page['posts'] = $this->loadFeed();

    }

    /**
     *  Prepare vars
     */
    protected function prepareVars()
    {
        /*
         * Page links
         */
        $this->postPage     = $this->page['postPage']     = $this->property('postPage');
        $this->categoryPage = $this->page['categoryPage'] = $this->property('categoryPage');
        $this->archivePage  = $this->page['archivePage']  = $this->property('archivePage');

    }

    /**
     *  Load feed
     */
    protected function loadFeed()
    {
        $posts = PostModel::getPressFeed();

        /*
         * Add a "url" helper attribute for linking to each post and category
         */
        $posts->each(function($post){
            $post->setUrl($this->postPage, $this->controller);

            $post->categories->each(function($category){
                $category->setUrl($this->categoryPage, $this->controller);
            });
        });

        CW::info(['PressFeed' => $posts]);

        return $posts;
    }
}