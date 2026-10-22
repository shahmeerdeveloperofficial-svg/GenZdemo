<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GenZBrandingTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_homepage_uses_the_genz_brand_and_theme_assets(): void
    {
        $response = $this->get('/');

        $response->assertOk()
            ->assertSee('GenZ Consultants')
            ->assertSee('Global Education and Career Development')
            ->assertSee('genz/css/theme.css')
            ->assertSee('genz-toast-host')
            ->assertSee('genz/js/backend-toast.js');
    }

    public function test_login_page_uses_the_shared_genz_brand(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('GenZ Consultants')
            ->assertSee('genz/css/backend.css')
            ->assertSee('genz-toast-host')
            ->assertSee('genz/js/backend-toast.js');
    }
}
