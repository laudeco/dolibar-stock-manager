<?php


namespace App\Repository;


interface DbManager
{

    /**
     * @param array $entity
     */
    public function save(array $entity);

    /**
     * @param mixed $id
     *
     * @return array
     */
    public function getById($id);

}