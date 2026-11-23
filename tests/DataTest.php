<?php

namespace Luany\Pt\Tests;

use PHPUnit\Framework\TestCase;

class DataTest extends TestCase
{
    public function test_data_is_format_date(): void
    {
        // Without an app the locale is English; the alias passes everything through.
        $this->assertSame(format_date('2026-11-23', 'long'), data('2026-11-23', 'long'));
        $this->assertSame('', data(null));
    }
}
