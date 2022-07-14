%%php%%

namespace App\Http\Controllers\{{ $domain }}BrowserAPI\{{ $classBaseName }};

use App\Http\Controllers\{{ $domain }}BrowserAPI\Base{{ $domain }}BrowserAPIController;
use App\Http\Presenters\PaginatorPresenter;
use App\Http\Requests\SearchRequest;
use App\Models\Search\{{ $domain }}API\{{ $classBaseName }}\{{ $classBaseName }}SearchQueryBuilder;
use Illuminate\Http\JsonResponse;

class {{ $classBaseName }}SearchController extends Base{{ $domain }}BrowserAPIController
{
    public function __invoke(SearchRequest $request): JsonResponse
    {
        $result = (new {{ $classBaseName }}SearchQueryBuilder())
            ->search($request->search, $request->orderBy)
            ->paginate($request->perPage);

        return $this->makeResponse((new PaginatorPresenter($result))->toArray());
    }
}
