<?php

namespace Luany\Pt;

use Luany\Core\Routing\Route;

/**
 * Rota — definição de rotas em português.
 *
 *   Rota::get('/', [InicioControlador::class, 'index'])->nome('inicio');
 *   Rota::recurso('produtos', ProdutoControlador::class);
 *   Rota::prefixo('/admin')->middleware(Autenticado::class)->grupo(function () {
 *       Rota::vista('/painel', 'admin.painel')->nome('admin.painel');
 *   });
 *
 * Os verbos HTTP (get, post, put, patch, delete) não se traduzem. Rota é uma
 * fachada sobre Route: as rotas ficam no mesmo router e Route continua a
 * funcionar ao lado.
 */
final class Rota
{
    public static function get(string $uri, mixed $acao, ?string $nome = null): RotaDefinida
    {
        return new RotaDefinida(Route::get($uri, $acao, $nome));
    }

    public static function post(string $uri, mixed $acao, ?string $nome = null): RotaDefinida
    {
        return new RotaDefinida(Route::post($uri, $acao, $nome));
    }

    public static function put(string $uri, mixed $acao, ?string $nome = null): RotaDefinida
    {
        return new RotaDefinida(Route::put($uri, $acao, $nome));
    }

    public static function patch(string $uri, mixed $acao, ?string $nome = null): RotaDefinida
    {
        return new RotaDefinida(Route::patch($uri, $acao, $nome));
    }

    public static function delete(string $uri, mixed $acao, ?string $nome = null): RotaDefinida
    {
        return new RotaDefinida(Route::delete($uri, $acao, $nome));
    }

    /** Qualquer método HTTP (= Route::any). */
    public static function qualquer(string $uri, mixed $acao, ?string $nome = null): RotaDefinida
    {
        return new RotaDefinida(Route::any($uri, $acao, $nome));
    }

    /**
     * As 7 rotas de um recurso. Opções: ['apenas' => [...]] / ['exceto' => [...]]
     * (ou only / except).
     *
     * @param array<string, mixed> $opcoes
     */
    public static function recurso(string $nome, string $controlador, array $opcoes = []): void
    {
        Route::resource($nome, $controlador, self::opcoes($opcoes));
    }

    /** @param array<string, mixed> $opcoes */
    public static function recursoApi(string $nome, string $controlador, array $opcoes = []): void
    {
        Route::apiResource($nome, $controlador, self::opcoes($opcoes));
    }

    /** @param array<string, mixed> $dados */
    public static function vista(string $uri, string $vista, array $dados = [], ?string $nome = null): RotaDefinida
    {
        return new RotaDefinida(Route::view($uri, $vista, $dados, $nome));
    }

    /**
     * Grupo: ['prefixo' => '/admin', 'middleware' => [...]] (ou prefix).
     *
     * @param array<string, mixed> $atributos
     */
    public static function grupo(array $atributos, callable $rotas): void
    {
        if (isset($atributos['prefixo'])) {
            $atributos['prefix'] = $atributos['prefixo'];
            unset($atributos['prefixo']);
        }
        Route::group($atributos, $rotas);
    }

    public static function prefixo(string $prefixo): GrupoRotas
    {
        return new GrupoRotas(Route::prefix($prefixo));
    }

    public static function middleware(mixed $middleware): GrupoRotas
    {
        return new GrupoRotas(Route::middleware($middleware));
    }

    /** Resolver próprio para um parâmetro: Rota::vincular('produto', fn ($id) => Produto::encontrar($id)). */
    public static function vincular(string $parametro, callable $resolver): void
    {
        Route::bind($parametro, $resolver);
    }

    /** @param class-string $classeModelo */
    public static function modelo(string $parametro, string $classeModelo): void
    {
        Route::model($parametro, $classeModelo);
    }

    /**
     * @param array<string, mixed> $opcoes
     * @return array<string, mixed>
     */
    private static function opcoes(array $opcoes): array
    {
        foreach (['apenas' => 'only', 'exceto' => 'except'] as $pt => $en) {
            if (isset($opcoes[$pt])) {
                $opcoes[$en] = $opcoes[$pt];
                unset($opcoes[$pt]);
            }
        }

        return $opcoes;
    }
}
