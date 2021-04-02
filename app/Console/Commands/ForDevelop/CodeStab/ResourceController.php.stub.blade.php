<?php

namespace {{ $namespace }};

use Illuminate\Http\JsonResponse;

class {{  $controllerName }} extends {{ $baseClassName }}
{
    public function index(): JsonResponse
    {
        $items = {{ $modelName }}::get()
            ->each(static function({{ $modelName }} $item){
                return new {{ $presenterName }}($item);
            })

        return $this->makeResponse($items);
    }

    public function show($id): JsonResponse
    {
        $item = {{ $modelName }}::findOrFail($id);

        return $this->makeResponse(new {{ $presenterName }}($item));
    }

    public function store({{ $createRequestClass }} $request, $id): JsonResponse
    {
        $item = new {{ $modelName }};
        $success = $item->fill($request->validated())->save();

        return $success
            ? $this->makeResponse(new {{ $presenterName }}($item), trans('response.{{ $modelName }}.store.success'))
            : $this->makeErrorResponse(trans('response.{{ $modelName }}.store.failed'));
    }

    public function update({{ $updateRequestClass }} $request, $id): JsonResponse
    {
        $item = {{ $modelName }}::findOrFail($id);
        $success = $item->fill($request->validated())->save();

        return $success
            ? $this->makeResponse(new {{ $presenterName }}($item), trans('response.{{ $modelName }}.update.success'))
            : $this->makeErrorResponse(trans('response.{{ $modelName }}.update.failed'));
    }

    public function delete($id): JsonResponse
    {
        $item = {{ $modelName }}::findOrFail($id);
        $success = $item->delete();

        return $success
            ? $this->makeResponse(new {{ $presenterName }}($item), trans('response.{{ $modelName }}.update.success'))
            : $this->makeErrorResponse(trans('response.{{ $modelName }}.update.failed'));
    }
}
