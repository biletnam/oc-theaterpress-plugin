<?php namespace Abnmt\TheaterPress;

use System\Classes\PluginBase;

use Abnmt\TheaterPress\Models\Post     as PostModel;
use Abnmt\TheaterPress\Models\Category as CategoryModel;

use Illuminate\Foundation\AliasLoader;
use Event;

/**
 * TheaterPress Plugin Information File
 */
class Plugin extends PluginBase
{

    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'abnmt.theaterpress::lang.plugin.name',
            'description' => 'abnmt.theaterpress::lang.plugin.description',
            'author'      => 'Abnmt',
            'icon'        => 'icon-presspaper-o'
        ];
    }

    public function registerNavigation()
    {
        return [
            'theaterpress' => [
                'label' => 'Новости',
                'url' => \Backend::url('abnmt/theaterpress/posts'),
                'icon' => 'icon-presspaper-o',
                'order' => 500,
                'sideMenu' => [
                    'posts' => [
                        'label' => 'Новости',
                        'icon'  => 'icon-presspaper-o',
                        'url'   => \Backend::url('abnmt/theaterpress/posts'),
                    ],
                    'categories' => [
                        'label' => 'Категории',
                        'icon'  => 'icon-list',
                        'url'   => \Backend::url('abnmt/theaterpress/categories'),
                    ],
                ],
            ],
        ];
    }

    /**
     * Register Components
     * @return array
     */
    public function registerComponents()
    {
        return [
            'Abnmt\TheaterPress\Components\Press'    => 'theaterPress',
            'Abnmt\TheaterPress\Components\Feed'    => 'theaterPressFeed',
            'Abnmt\TheaterPress\Components\Archive' => 'theaterPressArchive',
        ];
    }

    public function boot()
    {
        $alias = AliasLoader::getInstance();
        $alias->alias( 'Carbon', '\Carbon\Carbon' );
        $alias->alias( 'CW', '\Clockwork\Support\Laravel\Facade' );

        /*
         * Register menu items for the RainLab.Pages plugin
         */
        Event::listen('pages.menuitem.listTypes', function() {
            return [
                'pressarchive' => 'Архив новостей',
            ];
        });

        Event::listen('pages.menuitem.getTypeInfo', function($type) {
            if ($type == 'pressarchive')
                return PostModel::getMenuTypeInfo($type);
        });

        Event::listen('pages.menuitem.resolveItem', function($type, $item, $url, $theme) {
            if ($type == 'pressarchive')
                return PostModel::resolveMenuItem($item, $url, $theme);
        });
    }

}
