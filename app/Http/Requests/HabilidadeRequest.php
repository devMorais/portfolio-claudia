<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class HabilidadeRequest extends FormRequest
{
    /** Valor gravado no banco => rotulo mostrado na tela. */
    public const TIPOS = [
        'tecnica' => 'Técnica',
        'humana' => 'Humana',
    ];

    /** Os valores sem acento sao os que o site publico ja mostra hoje. */
    public const NIVEIS = [
        'Basico' => 'Básico',
        'Intermediario' => 'Intermediário',
        'Avancado' => 'Avançado',
    ];

    /** Fonte unica dos limites: a validacao e o maxlength da tela usam estes numeros. */
    public const LIMITES = [
        'nome' => 60,
        'categoria' => 40,
        'icone' => 40,
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
            'nome' => ['required', 'string', 'max:' . self::LIMITES['nome']],
            'tipo' => ['required', Rule::in(array_keys(self::TIPOS))],
            'categoria' => ['nullable', 'string', 'max:' . self::LIMITES['categoria']],
            'nivel' => ['nullable', Rule::in(array_keys(self::NIVEIS))],
            'descricao' => ['nullable', 'string', 'max:' . self::LIMITES['descricao']],
            'icone' => ['nullable', 'string', 'max:' . self::LIMITES['icone'], 'regex:/^[A-Za-z0-9-]+$/'],
        ];
    }

    public function attributes(): array
    {
        return [
            'nome' => 'nome',
            'tipo' => 'tipo',
            'categoria' => 'categoria',
            'nivel' => 'nível',
            'descricao' => 'descrição',
            'icone' => 'ícone',
        ];
    }

    public function messages(): array
    {
        return [
            'nome.required' => 'Preencha o nome da habilidade.',
            'tipo.required' => 'Escolha o tipo da habilidade.',
            'tipo.in' => 'O tipo precisa ser técnica ou humana.',
            'nivel.in' => 'Escolha um nível da lista.',
            'icone.regex' => 'Use só letras, números e hífen, como no nome do ícone (ex.: php, html5).',
            'string' => 'O :attribute precisa ser um texto.',
            'max.string' => 'O :attribute passou do limite de :max caracteres.',
        ];
    }
}