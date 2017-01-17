<?php

namespace App\Repository;

use App\Models\Photo;
use App\Dao\PhotoDao;

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

    public function __construct(PhotoDao $dao, $photoBuilder)
    {
        $this->dao = $dao;
        $this->photoBuilder = $photoBuilder;
    }
    public function newInstance($data)
    {
        return call_user_func_array($this->photoBuilder, [$data]);
    }
    public function getList($where)
    {
        $list = $this->dao->getList($where);
        $newList = [];
        foreach ($list as $key => $value) {
            $newList[] = $this->newInstance($value);
        }

        return $newList;
    }
    public function find($id)
    {
        $data = $this->dao->find($id);
        if ($data) {
            return $this->newInstance($this->dao->find($id));
        }
        throw new \Exception("Registro com ID: '{$id}' não foi encontrado");
    }
    public function insert(Photo $photo)
    {
        $insertId = $this->dao->insert($photo->toArray());
        if (!$insertId) {
            //@todo melhorar erro - talvez colocar na Dao
            throw new \Exception('Erro ao inserir registro');
        }
        $photo->id = $insertId;
    }
    public function update(Photo $photo)
    {
        $updated = $this->dao->update($photo->toArray(), ['id' => $photo->id]);
        if (!$updated) {
            //@todo melhorar erro - talvez colocar na Dao
            throw new \Exception('Erro ao atualizar registro');
        }
    }
    public function save(Photo $photo)
    {
        if ($photo->id) {
            return $this->update($photo);
        }

        return $this->insert($photo);
    }
    public function delete(Photo $photo)
    {
        $deleted = $this->dao->delete(['id' => $photo->id]);
        if (!$deleted) {
            throw new \Exception('Erro ao excluir registro');
        }

        // foreach ($photo as $key => $value) {
        //     $photo[$key] == null;
        // }

        return $deleted;
    }
}
