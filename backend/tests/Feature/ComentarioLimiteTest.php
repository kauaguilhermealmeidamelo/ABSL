<?php

namespace Tests\Feature;

use App\Models\Noticia;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ComentarioLimiteTest extends TestCase
{
    use RefreshDatabase;

    public function test_limite_300_via_validator(): void
    {
        $v300 = \Illuminate\Support\Facades\Validator::make(
            ['texto' => str_repeat('a', 300)],
            ['texto' => 'required|string|max:300']
        );
        $v301 = \Illuminate\Support\Facades\Validator::make(
            ['texto' => str_repeat('a', 301)],
            ['texto' => 'required|string|max:300']
        );
        $this->assertFalse($v300->fails());
        $this->assertTrue($v301->fails());

        // Controller usa a mesma regra (max:300) — garante backend alinhado.
        $src = file_get_contents(app_path('Http/Controllers/Api/NoticiaComentarioController.php'));
        $this->assertStringContainsString('max:300', $src);
    }
}
