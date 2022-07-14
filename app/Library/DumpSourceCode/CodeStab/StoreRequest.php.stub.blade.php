%%php%%

namespace App\Http\Requests\{{ $domain }}API\{{ $classBaseName }};

use App\Http\Requests\BaseFormRequest;
use {{ $classFullName }};

class {{ $classBaseName }}StoreRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return array_key_camel({{ $classBaseName }}::createRules());
    }

    public function attributes()
    {
        return array_key_camel({{ $classBaseName }}::ruleAttributes());
    }
}
