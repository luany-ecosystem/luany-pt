<?php

/*
 * Helpers em português (luany/pt). Cada um chama o helper inglês do
 * luany/framework; url(), e(), env(), asset() e flash não mudam de nome.
 */

use Luany\Core\Http\Response;

if (!function_exists('vista')) {
    /** @param array<string, mixed> $dados */
    function vista(string $nome, array $dados = []): string
    {
        return view($nome, $dados);
    }
}

if (!function_exists('redirecionar')) {
    function redirecionar(string $url, int $estado = 302): Response
    {
        return redirect($url, $estado);
    }
}

if (!function_exists('resposta')) {
    function resposta(string $corpo = '', int $estado = 200): Response
    {
        return response($corpo, $estado);
    }
}

if (!function_exists('validar')) {
    /**
     * @param array<string, mixed>  $dados
     * @param array<string, string|array<int, string>> $regras
     * @return array<string, mixed>
     */
    function validar(array $dados, array $regras, string $redirecionarPara = ''): array
    {
        return validate($dados, $regras, $redirecionarPara);
    }
}

if (!function_exists('rota')) {
    /** @param array<string, mixed> $parametros */
    function rota(string $nome, array $parametros = []): string
    {
        return route($nome, $parametros);
    }
}

if (!function_exists('configuracao')) {
    /** @param string|array<string, mixed>|null $chave */
    function configuracao(string|array|null $chave = null, mixed $padrao = null): mixed
    {
        return config($chave, $padrao);
    }
}

if (!function_exists('sessao')) {
    function sessao(?string $chave = null, mixed $padrao = null): mixed
    {
        return session($chave, $padrao);
    }
}

if (!function_exists('valorAnterior')) {
    function valorAnterior(string $chave, mixed $padrao = null): mixed
    {
        return old($chave, $padrao);
    }
}

if (!function_exists('abortar')) {
    /** @param array<string, string> $cabecalhos */
    function abortar(int $codigo, string $mensagem = '', array $cabecalhos = []): never
    {
        abort($codigo, $mensagem, $cabecalhos);
    }
}

if (!function_exists('traduzir')) {
    /** @param array<string, mixed> $substituir */
    function traduzir(string $chave, array $substituir = []): string
    {
        return __($chave, $substituir);
    }
}

if (!function_exists('registo')) {
    /** @param array<string, mixed> $contexto */
    function registo(string|\Stringable|null $mensagem = null, array $contexto = [], string $nivel = 'debug'): ?\Psr\Log\LoggerInterface
    {
        return logger($mensagem, $contexto, $nivel);
    }
}

if (!function_exists('registro')) {
    /**
     * Variante de registo() (pt-BR).
     *
     * @param array<string, mixed> $contexto
     */
    function registro(string|\Stringable|null $mensagem = null, array $contexto = [], string $nivel = 'debug'): ?\Psr\Log\LoggerInterface
    {
        return logger($mensagem, $contexto, $nivel);
    }
}

if (!function_exists('data')) {
    /**
     * Uma data no locale da aplicação (= format_date()):
     *   data($artigo->created_at);          // 23/11/2026
     *   data($artigo->created_at, 'long');  // 23 de novembro de 2026
     * Estilos: short, medium, long, full, time, datetime.
     */
    function data(\DateTimeInterface|string|int|null $valor, string $estilo = 'short'): string
    {
        return format_date($valor, $estilo);
    }
}
