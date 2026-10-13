<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TrajetoriaRequest extends FormRequest
{
    /** Valor gravado no banco => rotulo mostrado na tela. O valor define a cor no site. */
    public const AREAS = [
        'saude' => 'Saúde',
        'tecnologia' => 'Tecnologia',
    ];

    /** Fonte unica dos limites: a validacao e o maxlength da tela usam estes numeros. */
    public const LIMITES = [
        'periodo' => 30,
        'titulo' => 80,
        'subtitulo' => 100,
        'descricao' => 400,
    ];

    public function authorize(): bool
    {
        // A rota ja esta dentro do grupo 'auth'.
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->input('descricao'))) {
            $this->merge([
                'descricao' => str_replace("\r\n", "\n", $this->input('descricao')),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'periodo' => ['required', 'string', 'max:' . self::LIMITES['periodo']],
            'titulo' => ['required', 'string', 'max:' . self::LIMITES['titulo']],
            'subtitulo' => ['nullable', 'string', 'max:' . self::LIMITES['subtitulo']],
            'descricao' => ['nullable', 'string', 'max:' . self::LIMITES['descricao']],
            'area' => ['required', Rule::in(array_keys(self::AREAS))],
        ];
    }

    public function attributes(): array
    {
        return [
            'periodo' => 'período',
            'titulo' => 'título',
            'subtitulo' => 'subtítulo',
            'descricao' => 'descrição',
            'area' => 'área',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'Preencha o :attribute.',
            'area.required' => 'Escolha a área.',
            'area.in' => 'A área precisa ser saúde ou tecnologia.',
            'string' => 'O :attribute precisa ser um texto.',
            'max.string' => 'O :attribute passou do limite de :max caracteres.',
        ];
    }
}