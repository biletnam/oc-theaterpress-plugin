<?php namespace Abnmt\TheaterPress\Updates;

use System\Models\File as File;

use Abnmt\TheaterPress\Models\Article;
use Abnmt\TheaterPress\Models\Category;

use October\Rain\Database\Updates\Seeder;

class SeedPressTable extends Seeder
{

    public function run()
    {

        $data = require_once 'press.php';

        foreach ($data as $key => $model) {

            if (array_key_exists('category', $model)) {
                $categories = $model['category'];
                unset($model['category']);
            }

            if (array_key_exists('relations', $model)) {
                $relations = $model['relations'];
                unset($model['relations']);
            }

            $model = $this->createModel( 'Abnmt\TheaterPress\Models\Article', $model);

            if (isset($categories))
                $this->addTaxonomy('Abnmt\TheaterPress\Models\Category', $categories, $model);
            }

    }



    private function createModel($modelName, $model)
    {
        $model = $modelName::create($model);

        return $model;
    }


    private function addTaxonomy($taxonomyModelName, $categories, $model)
    {
        if (!is_array($categories)) $categories = [$categories];

        foreach ($categories as $key => $category) {
            $taxonomy = $taxonomyModelName::where('name', '=', $category)->first();

            if (is_null($taxonomy)) {
                $taxonomy = $taxonomyModelName::create(['name' => $category]);
            }

            if (!is_null($taxonomy)) {
                $model->categories()->add($taxonomy, null);
            }
        }

    }
}