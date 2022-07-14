%%php%%

namespace App\Http\Requests\{{ $domain }}API\{{ $classBaseName }};

use App\Http\Requests\BaseFormRequest;
use {{ $classFullName }};

class {{ $classBaseName }}UpdateRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return array_key_camel({{ $classBaseName }}::updateRules());
    }

    public function attributes()
    {
        return array_key_camel({{ $classBaseName }}::ruleAttributes());
    }
}
