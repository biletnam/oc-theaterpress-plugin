<?php namespace Abnmt\TheaterPress\Updates;

use System\Models\File as File;

use Abnmt\TheaterPress\Models\Post;
use Abnmt\TheaterPress\Models\Category;

use October\Rain\Database\Updates\Seeder;

class SeedPeopleTable extends Seeder
{

    public function run()
    {

        $data = require_once 'press.php';

        foreach ($data as $key => $model) {

            if (array_key_exists('category', $model)) {
                $categories = $model['category'];
                unset($model['category']);
            }

            $model = $this->createModel( 'Abnmt\TheaterPress\Models\Post', $model);

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