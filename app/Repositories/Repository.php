<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use App\Repositories\Interfaces\RepositoryInterface;

class Repository implements RepositoryInterface
{
    // model property on class instances
    protected $model;

    // Constructor to bind model to repo
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    // Get all instances of model
    public function all($columns = ['*'])
    {
        return $this->model->all($columns);
    }

    // create a new record in the database
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    // update record in the database
    public function update(array $data, $id)
    {
        $record = $this->model->find($id);
        return $record->update($data);
    }

    // remove record from the database
    public function delete($id)
    {
        return $this->model->destroy($id);
    }

    // show the record with the given id
    public function find($id, $columns = ['*'])
    {
        return $this->model->find($id);
    }

    // show the record with the given id with throw exception
    public function findOrFail($id, $columns = ['*'])
    {
        return $this->model->findOrFail($id);
    }

    // Eager load database relationships
    public function with($relations)
    {
        return $this->model->with($relations);
    }

    // Lazy load database relationships
    public function load($relations)
    {
        return $this->model->load($relations);
    }

    // Paginate model
    public function paginate($numberOfPages, $columns = ['*'], $pageNumber = 'page')
    {
        return $this->model->paginate($numberOfPages, $columns, $pageNumber);
    }

    // Get the associated model
    public function getModel()
    {
        return $this->model;
    }

    // Set the associated model
    public function setModel($model)
    {
        $this->model = $model;
        return $this;
    }
}
