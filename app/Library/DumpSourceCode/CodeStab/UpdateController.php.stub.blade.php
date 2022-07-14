%%php%%

namespace App\Http\Controllers\{{ $domain }}BrowserAPI\{{ $classBaseName }};

use App\Http\Controllers\{{ $domain }}BrowserAPI\Base{{ $domain }}BrowserAPIController;
use App\Http\Controllers\{{ $domain }}BrowserAPI\Presenters\{{ $classBaseName }}Presenter;
use App\Http\Requests\{{ $domain }}API\{{ $classBaseName }}\{{ $classBaseName }}UpdateRequest;
use {{ $classFullName }};
use Illuminate\Http\JsonResponse;

class {{ $classBaseName }}UpdateController extends Base{{ $domain }}BrowserAPIController
{
    /**
     * @param  {{ $classBaseName }}UpdateRequest $request
     * @param  int|string   ${{ \Str::camel($primaryKey) }}
     * @return JsonResponse
     */
    public function __invoke({{ $classBaseName }}UpdateRequest $request, int|string ${{ \Str::camel($primaryKey) }}): JsonResponse
    {
        ${{ \Str::camel($classBaseName) }} = {{ $classBaseName }}::findOrFail(${{ \Str::camel($primaryKey) }});

        $success = ${{ \Str::camel($classBaseName) }}->fill($request->validated())->save();

        return $success
            ? $this->makeResponse(new {{ $classBaseName }}Presenter(${{ \Str::camel($classBaseName) }}), '{{ $classNaturalName }}を更新しました。')
            : $this->makeErrorResponse('{{ $classNaturalName }}の更新に失敗しました。');
    }
}
