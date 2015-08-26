<?php namespace Abnmt\TheaterPress\Components;

use Cms\Classes\ComponentBase;
use Cms\Classes\Page;

use Abnmt\TheaterPress\Models\Article as ArticleModel;

use CW;

class Article extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name'        => 'abnmt.theaterpress::lang.components.article.name',
            'description' => 'abnmt.theaterpress::lang.components.article.description'
        ];
    }

    /**
     * @var The article model used for display.
     */
    public $article;

    /**
     * @var string Reference to the page name for linking to categories.
     */
    public $categoryPage;


    public function defineProperties()
    {
        return [
            'slug' => [
                'title'       => 'abnmt.theaterpress::lang.settings.article_slug',
                'description' => 'abnmt.theaterpress::lang.settings.article_slug_description',
                'default'     => '{{ :slug }}',
                'type'        => 'string'
            ],
            'categoryPage' => [
                'title'       => 'abnmt.theaterpress::lang.settings.article_category',
                'description' => 'abnmt.theaterpress::lang.settings.article_category_description',
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
        $this->article = $this->page['article'] = $this->loadArticle();
    }

    protected function loadArticle()
    {
        $slug = $this->property('slug');
        $article = ArticleModel::isPublished()->where('slug', $slug)->first();

        /*
         * Add a "url" helper attribute for linking to each category
         */
        if ($article && $article->categories->count()) {
            $article->categories->each(function($category){
                $category->setUrl($this->categoryPage, $this->controller);
            });
        }

        CW::info(['Press' => $article]);

        return $article;
    }

}