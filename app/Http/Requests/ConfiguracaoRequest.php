<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConfiguracaoRequest extends FormRequest
{
    /** Chave => [grupo, rotulo]. Fonte unica dos campos da tabela configuracoes. */
    public const CAMPOS = [
        'nome_completo' => ['geral', 'Nome completo'],
        'titulo_profissional' => ['geral', 'Titulo profissional'],
        'cidade' => ['geral', 'Cidade'],
        'email' => ['geral', 'E-mail de contato'],
        'curriculo_url' => ['geral', 'Curriculo em PDF'],
        'whatsapp' => ['redes', 'WhatsApp (so numeros, com DDD)'],
        'linkedin_url' => ['redes', 'LinkedIn'],
        'github_url' => ['redes', 'GitHub'],
        'instagram_url' => ['redes', 'Instagram'],
        'seo_titulo' => ['seo', 'Titulo da pagina'],
        'seo_descricao' => ['seo', 'Descricao (meta description)'],
        'seo_og_imagem' => ['seo', 'Imagem de compartilhamento (1200x630)'],
    ];

    /** Fonte unica dos limites: a validacao e o maxlength da tela usam estes numeros. */
    public const LIMITES = [
        'nome_completo' => 80,
        'titulo_profissional' => 80,
        'cidade' => 60,
        'email' => 120,
        'seo_titulo' => 70,
        'seo_descricao' => 160,
        'titulo_menu' => 30,
    ];

    public function authorize(): bool
    {
        // A rota ja esta dentro do grupo 'auth'.
        return true;
    }

    protected function prepareForValidation(): void
    {
        // (61) 99157-2752 vira 61991572752
        if (is_string($this->input('whatsapp'))) {
            $this->merge([
                'whatsapp' => preg_replace('/\D/', '', $this->input('whatsapp')),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'nome_completo' => ['required', 'string', 'max:' . self::LIMITES['nome_completo']],
            'titulo_profissional' => ['required', 'string', 'max:' . self::LIMITES['titulo_profissional']],
            'cidade' => ['nullable', 'string', 'max:' . self::LIMITES['cidade']],
            'email' => ['required', 'email', 'max:' . self::LIMITES['email']],
            'curriculo_url' => ['nullable', 'string', 'max:255'],
            'whatsapp' => ['nullable', 'regex:/^\d{10,13}$/'],
            'linkedin_url' => ['nullable', 'url:http,https', 'max:255'],
            'github_url' => ['nullable', 'url:http,https', 'max:255'],
            'instagram_url' => ['nullable', 'url:http,https', 'max:255'],
            'seo_titulo' => ['nullable', 'string', 'max:' . self::LIMITES['seo_titulo']],
            'seo_descricao' => ['nullable', 'string', 'max:' . self::LIMITES['seo_descricao']],
            'seo_og_imagem' => ['nullable', 'string', 'max:255'],
            'secoes_visiveis' => ['nullable', 'array'],
            'titulo_menu' => ['required', 'array'],
            'titulo_menu.*' => ['required', 'string', 'max:' . self::LIMITES['titulo_menu']],
        ];
    }

    public function attributes(): array
    {
        return [
            'nome_completo' => 'nome completo',
            'titulo_profissional' => 'título profissional',
            'cidade' => 'cidade',
            'email' => 'e-mail',
            'curriculo_url' => 'link do currículo',
            'whatsapp' => 'WhatsApp',
            'linkedin_url' => 'link do LinkedIn',
            'github_url' => 'link do GitHub',
            'instagram_url' => 'link do Instagram',
            'seo_titulo' => 'título da página',
            'seo_descricao' => 'descrição',
            'seo_og_imagem' => 'imagem de compartilhamento',
            'titulo_menu.*' => 'nome no menu',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'Preencha o :attribute.',
            'titulo_menu.*.required' => 'Preencha o nome que aparece no menu.',
            'email.email' => 'Esse e-mail não parece certo.',
            'whatsapp.regex' => 'Digite só números, com DDD. Ex.: 61999998888.',
            'url' => 'O :attribute precisa ser um link completo, começando com http:// ou https://.',
            'string' => 'O :attribute precisa ser um texto.',
            'max.string' => 'O :attribute passou do limite de :max caracteres.',
        ];
    }
}