<?php

namespace App\Models\Repository;

use App\Models\Photo;
use App\Models\Dao\PhotoDao;
use App\Models\Repository\Exception as RepositoryException;

class PhotoRepository
{
    /**
     * @var PhotoDao
     */
    protected $dao = null;
    /**
     * @var callable
     **/
    protected $photoBuilder = null;

    /**
     * @param PhotoDao - Object to access data
     * @param callable $photoBuilder- a callable to build new instance
     */
    public function __construct(PhotoDao $dao, $photoBuilder)
    {
        $this->dao = $dao;
        $this->photoBuilder = $photoBuilder;
    }

    /**
     * Create a new instance of Photo.
     * @param array $data
     * @return Photo
     */
    public function newInstance($data = [])
    {
        return call_user_func_array($this->photoBuilder, [$data]);
    }

    /**
     *  return a list of Photos.
     * @param array $where  ['id' => 1]
     * @param array|null $order  [field => ASC|DESC]
     * @param  int|null $limit
     * @param  int|null $offset
     */
    public function getList(array $where = [], array $order = null, $limit = null, $offset = null)
    {
        $list = $this->dao->getList($where, $order, $limit, $offset);
        $newList = [];
        foreach ($list as $key => $value) {
            $newList[] = $this->newInstance($value);
        }

        return $newList;
    }

    /**
     * finds a Photo by id.
     * @param int id
     */
    public function find($id)
    {
        $data = $this->dao->find($id);
        if ($data) {
            return $this->newInstance($data);
        }
        throw new RepositoryException("Registro com ID: '{$id}' não foi encontrado");
    }

    /**
     * Insert a new record on database.
     * @param Photo $photo
     * @return bool
     */
    public function insert(Photo $photo)
    {
        $insertId = $this->dao->insert($photo->toArray());
        if (!$insertId) {
            return false;
        }
        $photo->id = $insertId;

        return true;
    }

    /**
     * Update a Photo on database.
     * @param Photo $photo
     * @return bool
     */
    public function update(Photo $photo)
    {
        $updated = $this->dao->update($photo->toArray(), ['id' => $photo->id]);
        if (!$updated) {
            return false;
        }

        return (bool) $updated;
    }

    /**
     * Save a Photo on database.
     * @param Photo $photo
     * @return bool
     */
    public function save(Photo $photo)
    {
        if ($photo->id) {
            return $this->update($photo);
        }

        return $this->insert($photo);
    }

    /**
     * Delete a Photo on database.
     * @param Photo $photo
     * @return bool
     */
    public function delete(Photo $photo)
    {
        $deleted = $this->dao->delete(['id' => $photo->id]);
        if (!$deleted) {
            return false;
        }

        return $deleted;
    }
}
