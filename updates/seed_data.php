<?php namespace Abnmt\TheaterPress\Updates;

use System\Models\File as File;

use Abnmt\TheaterPress\Models\Article;
use Abnmt\TheaterPress\Models\Category;

use October\Rain\Database\Updates\Seeder;

class SeedPressTable extends Seeder
{

    public function run()
    {

        // echo 'Run' . "\n";

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

            // echo 'Create Model ' . $model['title'] . "\n";

            $model = $this->createModel( 'Abnmt\TheaterPress\Models\Article', $model);

            if (isset($categories)) {
                // echo 'Create Category ' . $model->title . "\n";
                $this->addTaxonomy('Abnmt\TheaterPress\Models\Category', $categories, $model);
            }

            if (isset($relations)) {
                // echo 'Create Category ' . $model->title . "\n";
                $this->addRelation('Abnmt\Theater\Models\Performance', $relations, $model);
            }
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

    private function addRelation($relationModel, $relations, $model)
    {

        foreach ($relations as $key => $relation) {
            $relation = $this->findModel($relationModel, $relation);

            if (!is_null($relation)) {
                // echo $relation['slug'] . "\n";

                $model->performances()->add($relation);
            }
        }
    }

    private function findModel($model, $name)
    {

        $post = $model::where('title', '=', $name)->first();
        if (!is_null($post)){
            return $post;
        }

        echo "Not find relation for " . $name . "\n" ;
        return NULL;
    }





}