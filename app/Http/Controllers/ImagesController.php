<?php

namespace App\Http\Controllers;

class ImagesController
{
    public function index($request, $response)
    {
        $collection = di()->make('PhotoRepository')->getList([], 'id desc');

        return view('images.index')
            ->with('collection', $collection);
    }
    public function show($request, $response)
    {
        return 'hello show world';
    }
    public function create($request, $response)
    {
        $resource = $collection = di()->make('PhotoRepository')->newInstance();

        return view('images.create')
            ->with('resource', $resource);
    }

    public function store($request, $response)
    {
        $uploadedFilePath = $this->handleUpload('file');
        if ($uploadedFilePath) {
            $repository = di()->make('PhotoRepository');
            $resource = $repository->newInstance($request->getPostParams());
            $pathArray = explode(upload_path(), $uploadedFilePath);
            $path = implode('', $pathArray);
            $resource->image = $path;
            $repository->save($resource);
            $response->setRedirect('/images');
            session()->flash()->add('success', 'Imagem cadastrada com sucesso!');
        } else {
            session()->flash()->add('danger', 'Ocorreu um erro ao cadastrar a imagem.');
            $response->setRedirect('/images/create');
        }
    }

    public function edit($request, $response)
    {
        $id = $request->getParam('id');
        $resource = $collection = di()->make('PhotoRepository')->find($id);

        return view('images.edit')
            ->with('resource', $resource);
    }
    public function update($request, $response)
    {
        $repository = di()->make('PhotoRepository');
        $resource = $repository->find($request->getParam('id'));
        foreach ($request->getPutParams() as $key => $value) {
            $resource->{$key} = $value;
        }
        $resource->id = $request->getParam('id');

        $uploadedFilePath = $this->handleUpload('file');

        $doUnlinkNewFn = $doUnlinkOldFn = function () {};//do nothing functions.

        if ($uploadedFilePath) {
            $oldFile = upload_path($resource->image);
            $doUnlinkOldFn = function () use ($oldFile) {
                if (is_file($oldFile)) {
                    unlink($oldFile);
                }
            };
            $doUnlinkNewFn = function () use ($uploadedFilePath) {
                if (is_file($uploadedFilePath)) {
                    unlink($uploadedFilePath);
                }
            };

            $pathArray = explode(upload_path(), $uploadedFilePath);
            $path = implode('', $pathArray);
            $resource->image = $path;
        }

        if ($repository->save($resource)) {
            $doUnlinkOldFn();
            $response->setRedirect('/images');
            session()->flash()->add('success', 'Imagem alterada com sucesso!');
        } else {
            session()->flash()->add('danger', 'Não foi possível alterar a imagem.');
            $doUnlinkNewFn();
            $response->setRedirect("/images/{$resource->id}/edit");
        }
    }

    public function destroy($request, $response)
    {
        $id = $request->getParam('id');
        $repository = di()->make('PhotoRepository');
        $photo = $repository->find($id);
        if ($photo) {
            $imagePath = upload_path($photo->image);
            if ($repository->delete($photo) && is_file($imagePath)) {
                unlink($imagePath);
                session()->flash()->add('success', 'Imagem excluída com sucesso!');
            } else {
                session()->flash()->add('danger', 'Ocorreu um erro ao excluir a imagem.');
            }
        } else {
            session()->flash()->add('warning', 'Não foi encontrar a imagem.');
        }

        $response->setRedirect('/images');
    }

    /**
     * Handle upload file.
     */
    protected function handleUpload($fieldName)
    {
        $fileParam = $_FILES[$fieldName];
        if ($fileParam) {
            $uploadDir = upload_path('images/');
            if (!is_dir($uploadDir)) {
                @mkdir($uploadDir, '0777', true);
            }
            $fileName = $fileParam['name'];
            $extension = pathinfo($fileName, PATHINFO_EXTENSION);
            $newName = uniqid('img_').'.'.$extension;
            $uploadedFilePath = implode('/', [rtrim($uploadDir, '/'), $newName]);
            if (move_uploaded_file($fileParam['tmp_name'], $uploadedFilePath)) {
                return $uploadedFilePath;
            };
        }

        return;
    }
}
