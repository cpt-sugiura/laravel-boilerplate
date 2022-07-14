%%php%%

namespace App\Http\Controllers\{{ $domain }}BrowserAPI\{{ $classBaseName }};

use App\Http\Controllers\{{ $domain }}BrowserAPI\Base{{ $domain }}BrowserAPIController;
use App\Http\Controllers\{{ $domain }}BrowserAPI\Presenters\{{ $classBaseName }}Presenter;
use App\Http\Requests\{{ $domain }}API\{{ $classBaseName }}\{{ $classBaseName }}StoreRequest;
use {{ $classFullName }};
use Illuminate\Http\JsonResponse;

class {{ $classBaseName }}StoreController extends Base{{ $domain }}BrowserAPIController
{
    /**
     * @param  {{ $classBaseName }}StoreRequest $request
     * @return JsonResponse
     */
    public function __invoke({{ $classBaseName }}StoreRequest $request): JsonResponse
    {
        ${{ \Str::camel($classBaseName) }} = new {{ $classBaseName }}();

        $success = ${{ \Str::camel($classBaseName) }}->fill($request->validated())->save();

        return $success
            ? $this->makeResponse(new {{ $classBaseName }}Presenter(${{ \Str::camel($classBaseName) }}), '{{ $classNaturalName }}を作成しました。')
            : $this->makeErrorResponse('{{ $classNaturalName }}の作成に失敗しました。');
    }
}
