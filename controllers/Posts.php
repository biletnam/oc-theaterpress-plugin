<?php namespace Abnmt\TheaterPress\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use Flash;
use Lang;

/**
 * Articles Back-end Controller
 */
class Articles extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController'
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Abnmt.TheaterPress', 'theaterpress', 'articles');
    }

    /**
     * Deleted checked articles.
     */
    public function index_onDelete()
    {
        if (($checkedIds = post('checked')) && is_array($checkedIds) && count($checkedIds)) {

            foreach ($checkedIds as $postId) {
                if (!$post = Article::find($postId)) continue;
                $post->delete();
            }

            Flash::success(Lang::get('abnmt.theaterpress::lang.articles.delete_selected_success'));
        }
        else {
            Flash::error(Lang::get('abnmt.theaterpress::lang.articles.delete_selected_empty'));
        }

        return $this->listRefresh();
    }
}