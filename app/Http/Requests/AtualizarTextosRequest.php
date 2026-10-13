<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AtualizarTextosRequest extends FormRequest
{
    /**
     * Limite de caracteres de cada campo. Fonte unica: a validacao usa
     * estes numeros e a view usa os mesmos no maxlength.
     */
    public const LIMITES = [
        'hero.sobretitulo' => 50,
        'hero.titulo' => 50,
        'hero.texto' => 400,
        'sobre.titulo' => 60,
        'sobre.subtitulo' => 120,
        'sobre.ato_1_titulo' => 40,
        'sobre.ato_1_texto' => 550,
        'sobre.ato_2_titulo' => 40,
        'sobre.ato_2_texto' => 550,
        'sobre.ato_3_titulo' => 40,
        'sobre.ato_3_texto' => 550,
    ];

    private const OBRIGATORIOS = ['hero.titulo', 'sobre.titulo'];

    protected function prepareForValidation(): void
    {
        $dados = $this->all();

        foreach (array_keys(self::LIMITES) as $campo) {
            $valor = data_get($dados, $campo);

            if (is_string($valor)) {
                data_set($dados, $campo, str_replace("\r\n", "\n", $valor));
            }
        }

        $this->merge($dados);
    }

    public function authorize(): bool
    {
        // A rota ja esta dentro do grupo 'auth'.
        return true;
    }

    public function rules(): array
    {
        $regras = [];

        foreach (self::LIMITES as $campo => $limite) {
            $regras[$campo] = [
                in_array($campo, self::OBRIGATORIOS, true) ? 'required' : 'nullable',
                'string',
                "max:{$limite}",
            ];
        }

        return $regras;
    }

    public function attributes(): array
    {
        return [
            'hero.sobretitulo' => 'sobretítulo',
            'hero.titulo' => 'título da apresentação',
            'hero.texto' => 'texto da apresentação',
            'sobre.titulo' => 'título da história',
            'sobre.subtitulo' => 'subtítulo da história',
            'sobre.ato_1_titulo' => 'título do ato 1',
            'sobre.ato_1_texto' => 'texto do ato 1',
            'sobre.ato_2_titulo' => 'título do ato 2',
            'sobre.ato_2_texto' => 'texto do ato 2',
            'sobre.ato_3_titulo' => 'título do ato 3',
            'sobre.ato_3_texto' => 'texto do ato 3',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'Preencha o :attribute.',
            'string' => 'O :attribute precisa ser um texto.',
            'max.string' => 'O :attribute passou do limite de :max caracteres.',
        ];
    }
}