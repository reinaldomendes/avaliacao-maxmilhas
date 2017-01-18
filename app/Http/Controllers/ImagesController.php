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
        return 'hello create world'.
        '<form action="/images" method="POST">
            <input type="hidden" name="val" value="valor" />
            <button type="submit">Create</button>
        </form>';
    }

    public function store($request, $response)
    {
        return 'Hello Store World';
    }

    public function edit($request, $response)
    {
        return 'hello edit world'.
        '<form action="/images/1" method="POST">
            <input type="hidden" name="_method" value="PUT" />
            <input type="hidden" name="val" value="valor" />
            <button type="submit">Update</button>
        </form>';
    }
    public function update($request, $response)
    {
        return 'Hello Update World';
    }

    public function destroy($request, $response)
    {
        $id = $request->getParam('id');
        $repository = di()->make('PhotoRepository');
        $photo = $repository->find($id);
        if ($photo) {
            $repository->delete($photo);
        }

        $response->setRedirect('/images');
    }
}
