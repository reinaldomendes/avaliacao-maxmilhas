<?php

namespace App\Http\Controllers;

use Rbm\Http\Request;

class ImagesController
{
    /**
     * @request GET
     * Exibe a listagem das imagens.
     * @param Request $request
     * @param Response $request
     */
    public function index($request, $response)
    {
        $collection = di()->make('PhotoRepository')->getList([], ['id' => 'desc']);

        return view('images.index')
            ->with('collection', $collection);
    }

    /**
     * @request GET
     * Exibe o formulário para cadastro de uma nova imagem.
     * @param Request $request
     * @param Response $request
     */
    public function create($request, $response)
    {
        $resource = $collection = di()->make('PhotoRepository')->newInstance();

        return view('images.create')
            ->with('resource', $resource);
    }

    /**
     * Salva uma nova imagem no banco de dados e faz o upload.
     * @request POST
     * @param Request $request
     * @param Response $request
     */
    public function store($request, $response)
    {
        if ($this->hasUpload('file')) {
            $uploadedFilePath = $this->handleUpload('file');
            $repository = di()->make('PhotoRepository');
            $resource = $repository->newInstance($request->getPostParams());
            $pathArray = explode(upload_path(), $uploadedFilePath);
            $path = implode('', $pathArray);
            $resource->image = $path;
            if ($repository->save($resource)) {
                $response->setRedirect('/images');
                session()->flash()->add('success', 'Imagem cadastrada com sucesso!');
            } else {
                unlink(upload_path($path)); //remove a imagem enviada
                session()->flash()->add('danger', 'Ocorreu um erro ao salvar a imagem.');
            }
        } else {
            session()->flash()->add('danger', 'Ocorreu um erro ao realizar o upload do arquivo tente novamente.');
            $response->setRedirect('/images/create');
        }
    }
    /**
     *  Exibe o formulário para edição de uma nova imagem.
     * @request GET
     * @param Request $request
     * @param Response $request
     */
    public function edit($request, $response)
    {
        $id = $request->getParam('id');
        $resource = $collection = di()->make('PhotoRepository')->find($id);

        return view('images.edit')
            ->with('resource', $resource);
    }
    /**
     * Edita uma imagem.
     * @request PUT
     * @param Request $request
     * @param Response $request
     */
    public function update($request, $response)
    {
        $repository = di()->make('PhotoRepository');
        $resource = $repository->find($request->getParam('id'));
        foreach ($request->getPutParams() as $key => $value) {
            $resource->{$key} = $value;
        }
        $resource->id = $request->getParam('id');

        $unlinkNewUploadedImageFn = $unlinkOldImageFn = function () {};//do nothing functions.

        if ($this->hasUpload('file')) {
            $uploadedFilePath = $this->handleUpload('file');
            $oldFile = upload_path($resource->image);

            $unlinkOldImageFn = function () use ($oldFile) {
                if (is_file($oldFile)) {
                    unlink($oldFile);
                }
            };
            $unlinkNewUploadedImageFn = function () use ($uploadedFilePath) {
                if (is_file($uploadedFilePath)) {
                    unlink($uploadedFilePath);
                }
            };
            $pathArray = explode(upload_path(), $uploadedFilePath);
            $path = implode('', $pathArray);
            $resource->image = $path;
        }

        if ($repository->save($resource)) {
            $unlinkOldImageFn();
            $response->setRedirect('/images');
            session()->flash()->add('success', 'Imagem alterada com sucesso!');
        } else {
            session()->flash()->add('danger', 'Não foi possível alterar a imagem.');
            $unlinkNewUploadedImageFn();
            $response->setRedirect("/images/{$resource->id}/edit");
        }
    }

    /**
     * Exclui uma imagem.
     * @request DELETE
     * @param Request $request
     * @param Response $request
     */
    public function destroy($request, $response)
    {
        $id = $request->getParam('id');
        $repository = di()->make('PhotoRepository');
        $photo = $repository->find($id);
        if ($photo) {
            $imagePath = upload_path($photo->image);

            if ($repository->delete($photo)) {
                is_file($imagePath) && unlink($imagePath);

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
     * Checa se há um arquivo de upload.
     */
    protected function hasUpload($fieldName)
    {
        return isset($_FILES[$fieldName]) && $_FILES[$fieldName]['size'] > 0;
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
            }
        }

        return;
    }
}
