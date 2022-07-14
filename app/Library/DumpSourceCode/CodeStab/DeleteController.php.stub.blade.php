%%php%%

namespace App\Http\Controllers\{{ $domain }}BrowserAPI\{{ $classBaseName }};

use App\Http\Controllers\{{ $domain }}BrowserAPI\Base{{ $domain }}BrowserAPIController;
use App\Http\Controllers\{{ $domain }}BrowserAPI\Presenters\{{ $classBaseName }}Presenter;
use {{ $classFullName }};
use Illuminate\Http\JsonResponse;

class {{ $classBaseName }}DeleteController extends Base{{ $domain }}BrowserAPIController
{
    /**
     * @param  int|string ${{ \Str::camel($primaryKey) }}
     * @return JsonResponse
     */
    public function __invoke(int|string ${{ \Str::camel($primaryKey) }}): JsonResponse
    {
        ${{ \Str::camel($classBaseName) }} = {{ $classBaseName }}::findOrFail(${{ \Str::camel($primaryKey) }});

        $success = ${{ \Str::camel($classBaseName) }}->delete();

        return $success
            ? $this->makeResponse(new {{ $classBaseName }}Presenter(${{ \Str::camel($classBaseName) }}), '{{ $classNaturalName }}を削除しました。')
            : $this->makeErrorResponse('{{ $classNaturalName }}の削除に失敗しました。');
    }
}
