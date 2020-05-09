<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

class Repository implements RepositoryInterface
{
    protected $model;

    /**
     * Repository constructor.
     *
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Get all instances of model
     *
     * @return \Illuminate\Database\Eloquent\Collection|Model[]
     */
    public function all()
    {
        return $this->model->all();
    }

    /**
     * create a new record in the database
     *
     * @param array $data
     * @return Model
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * update record in the database
     *
     * @param array $data
     * @param $id
     * @return mixed
     */
    public function update(array $data, $id)
    {
        $record = $this->model->find($id);
        return $record->update($data);
    }

    /**
     * remove record from the database
     *
     * @param $id
     * @return int
     */
    public function delete($id)
    {
        return $this->model->destroy($id);
    }

    /**
     * show the record with the given id
     *
     * @param $id
     * @return Model
     */
    public function show($id)
    {
        return $this->model->findOrFail($id);
    }

    /**
     * delete all data from the model table
     */
    public function truncate()
    {
        $this->model->truncate();
    }

    /**
     * insert all the record into the model table
     *
     * @param array $data
     */
    public function insert(array $data)
    {
        $this->model->insert($data);
    }
}