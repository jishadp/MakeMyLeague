<?php

namespace App\Relations;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

class JsonArrayRelation extends Relation
{
    /**
     * The JSON array field on the parent model.
     *
     * @var string
     */
    protected $jsonField;

    /**
     * Create a new JSON array relation instance.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \Illuminate\Database\Eloquent\Model  $parent
     * @param  string  $jsonField
     * @return void
     */
    public function __construct(Builder $query, Model $parent, string $jsonField)
    {
        $this->jsonField = $jsonField;
        
        parent::__construct($query, $parent);
    }

    /**
     * Set the base constraints on the relation query.
     *
     * @return void
     */
    public function addConstraints()
    {
        if (static::$constraints) {
            $ids = $this->getParentJsonIds();
            
            // If we have IDs, add a whereIn constraint
            if (count($ids) > 0) {
                $this->query->whereIn($this->related->getKeyName(), $ids);
            } else {
                // If no IDs, ensure we get an empty result set
                $this->query->whereRaw('1 = 0');
            }
        }
    }

    /**
     * Set the constraints for an eager load of the relation.
     *
     * @param  array  $models
     * @return void
     */
    public function addEagerConstraints(array $models)
    {
        // Collect all IDs from the JSON fields of all models
        $ids = collect($models)
            ->map(function ($model) {
                return $model->{$this->jsonField} ?? [];
            })
            ->flatten()
            ->unique()
            ->values()
            ->all();
            
        // Apply the constraint
        if (count($ids) > 0) {
            $this->query->whereIn($this->related->getKeyName(), $ids);
        }
    }

    /**
     * Initialize the relation on a set of models.
     *
     * @param  array  $models
     * @param  string  $relation
     * @return array
     */
    public function initRelation(array $models, $relation)
    {
        foreach ($models as $model) {
            $model->setRelation($relation, $this->related->newCollection());
        }

        return $models;
    }

    /**
     * Match the eagerly loaded results to their parents.
     *
     * @param  array  $models
     * @param  \Illuminate\Database\Eloquent\Collection  $results
     * @param  string  $relation
     * @return array
     */
    public function match(array $models, Collection $results, $relation)
    {
        // Index the results by key
        $dictionary = $results->keyBy($this->related->getKeyName())->all();

        // Match each model with its related results based on the JSON array
        foreach ($models as $model) {
            $ids = $model->{$this->jsonField} ?? [];
            $relatedModels = collect();
            
            foreach ($ids as $id) {
                if (isset($dictionary[$id])) {
                    $relatedModels->push($dictionary[$id]);
                }
            }
            
            $model->setRelation($relation, $relatedModels);
        }

        return $models;
    }

    /**
     * Get the results of the relationship.
     *
     * @return mixed
     */
    public function getResults()
    {
        $ids = $this->getParentJsonIds();
        
        if (count($ids) === 0) {
            return $this->related->newCollection();
        }
        
        return $this->query->get();
    }

    /**
     * Get the IDs from the parent model's JSON field.
     *
     * @return array
     */
    protected function getParentJsonIds()
    {
        $ids = $this->parent->{$this->jsonField} ?? [];
        
        if (!is_array($ids)) {
            $ids = [];
        }
        
        return $ids;
    }
}
