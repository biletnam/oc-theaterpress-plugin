<?php namespace Abnmt\TheaterPress\Components;

use Cms\Classes\ComponentBase;
use Cms\Classes\Page;

use Abnmt\TheaterPress\Models\Article     as ArticleModel;
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
     * A collection of articles to display
     * @var Collection
     */
    public $articles;

    /**
     * Parameter to use for the page number
     * @var string
     */
    public $pageParam;

    /**
     * If the article list should be filtered by a category, the model to use.
     * @var Model
     */
    public $category;

    /**
     * Reference to the page name for linking to articles.
     * @var string
     */
    public $articlePage;

    /**
     * Reference to the page name for linking to categories.
     * @var string
     */
    public $categoryPage;

    public function defineProperties()
    {
        return [
            'pageNumber' => [
                'title'       => 'abnmt.theaterpress::lang.settings.articles_pagination',
                'description' => 'abnmt.theaterpress::lang.settings.articles_pagination_description',
                'type'        => 'string',
                'default'     => '{{ :page }}',
            ],
            'categoryFilter' => [
                'title'       => 'abnmt.theaterpress::lang.settings.articles_filter',
                'description' => 'abnmt.theaterpress::lang.settings.articles_filter_description',
                'type'        => 'string',
                'default'     => ''
            ],
            'articlesPerPage' => [
                'title'             => 'abnmt.theaterpress::lang.settings.articles_per_page',
                'type'              => 'string',
                'validationPattern' => '^[0-9]+$',
                'validationMessage' => 'abnmt.theaterpress::lang.settings.articles_per_page_validation',
                'default'           => '10',
            ],
            'categoryPage' => [
                'title'       => 'abnmt.theaterpress::lang.settings.articles_category',
                'description' => 'abnmt.theaterpress::lang.settings.articles_category_description',
                'type'        => 'dropdown',
                'default'     => 'theaterPress/category',
                'group'       => 'Страницы',
            ],
            'articlePage' => [
                'title'       => 'abnmt.theaterpress::lang.settings.articles_article',
                'description' => 'abnmt.theaterpress::lang.settings.articles_article_description',
                'type'        => 'dropdown',
                'default'     => 'theaterPress/article',
                'group'       => 'Страницы',
            ],
        ];
    }

    public function getCategoryPageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }
    public function getArticlePageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }


    public function onRun()
    {
        $this->prepareVars();

        $this->category = $this->page['category'] = $this->loadCategory();
        $this->articles    = $this->page['articles']    = $this->listArticles();

        /*
         * If the page number is not valid, redirect
         */
        if ($pageNumberParam = $this->paramName('pageNumber')) {
            $currentPage = $this->property('pageNumber');

            if ($currentPage > ($lastPage = $this->articles->lastPage()) && $currentPage > 1)
                return Redirect::to($this->currentPageUrl([$pageNumberParam => $lastPage]));
        }
    }

    protected function prepareVars()
    {
        $this->pageParam      = $this->page['pageParam']      = $this->paramName('pageNumber');

        /*
         * Page links
         */
        $this->articlePage     = $this->page['articlePage']     = $this->property('articlePage');
        $this->categoryPage = $this->page['categoryPage'] = $this->property('categoryPage');
    }

    protected function listArticles()
    {
        $categories = $this->category ? $this->category->id : null;

        /*
         * List all the articles, eager load their categories
         */
        $articles = ArticleModel::with('categories')->listFrontEnd([
            'page'       => $this->property('pageNumber'),
            'perPage'    => $this->property('articlesPerPage'),
            'categories' => $categories
        ]);

        /*
         * Add a "url" helper attribute for linking to each article and category
         */
        $articles->each(function($article){
            $article->setUrl($this->articlePage, $this->controller);

            $article->categories->each(function($category){
                $category->setUrl($this->categoryPage, $this->controller);
            });
        });

        CW::info(['PressArchive' => $articles]);

        return $articles;
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