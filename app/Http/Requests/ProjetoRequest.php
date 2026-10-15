<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProjetoRequest extends FormRequest
{
    /** Fonte unica dos limites: a validacao e o maxlength da tela usam estes numeros. */
    public const LIMITES = [
        'nome' => 80,
        'resumo' => 200,
        'descricao' => 1000,
        'papel' => 40,
        'tecnologias' => 120,
    ];

    /** 4 MB. O PHP (upload_max_filesize) e o servidor precisam aceitar pelo menos isso. */
    public const IMAGEM_MAX_KB = 4096;

    public function authorize(): bool
    {
        // A rota ja esta dentro do grupo 'auth'.
        return true;
    }

    protected function prepareForValidation(): void
    {
        $ajustes = [
            // Checkbox desmarcado nao e enviado: vira false.
            'destaque' => $this->boolean('destaque'),
        ];

        foreach (['resumo', 'descricao'] as $campo) {
            if (is_string($this->input($campo))) {
                $ajustes[$campo] = str_replace("\r\n", "\n", $this->input($campo));
            }
        }

        $this->merge($ajustes);
    }

    public function rules(): array
    {
        return [
            'nome' => ['required', 'string', 'max:' . self::LIMITES['nome']],
            'resumo' => ['nullable', 'string', 'max:' . self::LIMITES['resumo']],
            'descricao' => ['nullable', 'string', 'max:' . self::LIMITES['descricao']],
            'papel' => ['nullable', 'string', 'max:' . self::LIMITES['papel']],
            'tecnologias' => ['nullable', 'string', 'max:' . self::LIMITES['tecnologias']],
            'repositorio_url' => ['nullable', 'url:http,https', 'max:255'],
            'site_url' => ['nullable', 'url:http,https', 'max:255'],
            'destaque' => ['boolean'],
            'imagem' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:' . self::IMAGEM_MAX_KB],
            'remover_imagem' => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'nome' => 'nome',
            'resumo' => 'resumo',
            'descricao' => 'descrição',
            'papel' => 'papel',
            'tecnologias' => 'campo de tecnologias',
            'repositorio_url' => 'link do repositório',
            'site_url' => 'link do site',
            'imagem' => 'imagem',
        ];
    }

    public function messages(): array
    {
        return [
            'nome.required' => 'Preencha o nome do projeto.',
            'url' => 'O :attribute precisa ser um link completo, começando com http:// ou https://.',
            'imagem.mimes' => 'A imagem precisa ser JPG, PNG ou WEBP.',
            'imagem.max' => 'A imagem passou de 4 MB.',
            'imagem.uploaded' => 'Não consegui enviar a imagem. Ela pode ter mais de 4 MB.',
            'imagem.file' => 'Não consegui enviar a imagem. Escolha o arquivo de novo.',
            'string' => 'O :attribute precisa ser um texto.',
            'max.string' => 'O :attribute passou do limite de :max caracteres.',
        ];
    }
}