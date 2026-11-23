<?php

namespace Luany\Pt\Tests;

use Luany\Core\Routing\Route;
use Luany\Pt\ChaveEstrangeira;
use Luany\Pt\Rota;
use PHPUnit\Framework\TestCase;

class RotaTest extends TestCase
{
    protected function setUp(): void
    {
        Route::reset();
    }

    /** @return array<string, array<string, mixed>> uri => route */
    private function routes(): array
    {
        $byUri = [];
        foreach (Route::getRoutes() as $route) {
            $byUri[implode('|', (array) ($route['methods'] ?? $route['method'] ?? [])) . ' ' . $route['uri']] = $route;
        }
        return $byUri;
    }

    public function test_verbs_names_and_url_helper(): void
    {
        Rota::get('/', fn () => 'ok')->nome('inicio');
        Rota::post('/contacto', fn () => 'ok')->nome('contacto.enviar')->middleware('throttle');
        Rota::qualquer('/eco', fn () => 'ok');

        $this->assertContains(rota('inicio'), ['', '/'], 'the root route');
        $this->assertSame('/contacto', rota('contacto.enviar'));
        $this->assertCount(3, Route::getRoutes());
    }

    public function test_resources_with_portuguese_options(): void
    {
        Rota::recurso('produtos', 'App\\Controllers\\ProdutoControlador', ['apenas' => ['index', 'show']]);
        Rota::recursoApi('categorias', 'App\\Controllers\\CategoriaControlador', ['exceto' => ['destroy']]);

        $uris = array_column(Route::getRoutes(), 'uri');
        $this->assertContains('/produtos', $uris);
        $this->assertNotContains('/produtos/create', $uris);
        $this->assertNotContains('/categorias/create', $uris);
        $this->assertCount(2 + 5, Route::getRoutes(), 'index+show; api: index, store, show, update (PUT and PATCH)');
    }

    public function test_groups_and_prefixes(): void
    {
        Rota::prefixo('/admin')->middleware('auth')->grupo(function () {
            Rota::vista('/painel', 'admin.painel')->nome('admin.painel');
        });
        Rota::grupo(['prefixo' => '/api'], function () {
            Rota::get('/estado', fn () => 'ok')->nome('api.estado');
        });

        $this->assertSame('/admin/painel', rota('admin.painel'));
        $this->assertSame('/api/estado', rota('api.estado'));
    }

    public function test_portuguese_plural_for_constrained_tables(): void
    {
        $this->assertSame('categorias', ChaveEstrangeira::tabelaPt('categoria_id'));
        $this->assertSame('autores', ChaveEstrangeira::tabelaPt('autor_id'));
        $this->assertSame('papeis', ChaveEstrangeira::tabelaPt('papel_id'));
        $this->assertSame('itens', ChaveEstrangeira::tabelaPt('item_id'));
        $this->assertSame('cidades', ChaveEstrangeira::tabelaPt('cidade_id'));
        $this->assertSame('paises', ChaveEstrangeira::tabelaPt('pais_id'));
        $this->assertSame('cancoes', ChaveEstrangeira::tabelaPt('cancao_id'));
    }
}
