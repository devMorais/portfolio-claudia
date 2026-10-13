<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FormacaoRequest extends FormRequest
{
    /** Fonte unica dos limites: a validacao e o maxlength da tela usam estes numeros. */
    public const LIMITES = [
        'titulo' => 100,
        'instituicao' => 100,
        'ano' => 30,
        'carga_horaria' => 30,
    ];

    public function authorize(): bool
    {
        // A rota ja esta dentro do grupo 'auth'.
        return true;
    }

    public function rules(): array
    {
        return [
            'titulo' => ['required', 'string', 'max:' . self::LIMITES['titulo']],
            'instituicao' => ['nullable', 'string', 'max:' . self::LIMITES['instituicao']],
            'ano' => ['nullable', 'string', 'max:' . self::LIMITES['ano']],
            'carga_horaria' => ['nullable', 'string', 'max:' . self::LIMITES['carga_horaria']],
            'certificado_url' => ['nullable', 'url:http,https', 'max:255'],
        ];
    }

    public function attributes(): array
    {
        return [
            'titulo' => 'título',
            'instituicao' => 'instituição',
            'ano' => 'ano',
            'carga_horaria' => 'carga horária',
            'certificado_url' => 'link do certificado',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'Preencha o :attribute.',
            'url' => 'O :attribute precisa ser um link completo, começando com http:// ou https://.',
            'string' => 'O :attribute precisa ser um texto.',
            'max.string' => 'O :attribute passou do limite de :max caracteres.',
        ];
    }
}