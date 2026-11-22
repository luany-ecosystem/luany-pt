<?php

namespace Luany\Pt\Tests;

use PHPUnit\Framework\TestCase;

/**
 * resources/aliases.pt.json is the single source: every Portuguese name it
 * lists exists, and so does the English name it stands for.
 */
class TabelaDeAliasesTest extends TestCase
{
    /** @return list<array<string, mixed>> */
    private function entries(): array
    {
        $data = json_decode((string) file_get_contents(dirname(__DIR__) . '/resources/aliases.pt.json'), true);
        $this->assertIsArray($data);

        return $data['entries'];
    }

    public function test_every_method_alias_exists_on_both_sides(): void
    {
        foreach ($this->entries() as $e) {
            if ($e['kind'] !== 'method') {
                continue;
            }
            $this->assertTrue(method_exists($e['class'], $e['pt']), "{$e['class']}::{$e['pt']} missing");
            $this->assertTrue(
                method_exists($e['en_class'], $e['en']),
                "{$e['en_class']}::{$e['en']} missing"
            );
        }
    }

    public function test_every_function_alias_exists(): void
    {
        foreach ($this->entries() as $e) {
            if ($e['kind'] !== 'function') {
                continue;
            }
            foreach ([$e['pt'], ...$e['variants']] as $name) {
                $this->assertTrue(function_exists($name), "{$name}() missing");
            }
            $this->assertTrue(function_exists($e['en']), "{$e['en']}() missing in luany/framework");
        }
    }

    public function test_portuguese_names_are_ascii(): void
    {
        foreach ($this->entries() as $e) {
            $this->assertMatchesRegularExpression('/^[A-Za-z_]+$/', $e['pt'], $e['id']);
        }
    }
}
