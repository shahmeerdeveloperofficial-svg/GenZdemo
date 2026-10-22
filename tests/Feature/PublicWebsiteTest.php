<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicWebsiteTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_homepage_renders_with_empty_catalogue_states(): void
    {
        $response = $this->get('/');

        $response->assertOk()
            ->assertSee('Study destinations')
            ->assertSee('Featured universities')
            ->assertSee('Destination guides are being prepared.');
    }

    public function test_contact_page_discloses_demo_contact_details_and_whatsapp_action(): void
    {
        $response = $this->get('/contact-us');

        $response->assertOk()
            ->assertSee('Sample contact information for demonstration only')
            ->assertSee('hello@genzconsultants.example')
            ->assertSee('Message us on WhatsApp');
    }

    public function test_public_sitemap_is_valid_xml_and_contains_public_entry_points(): void
    {
        $response = $this->get('/sitemap.xml');

        $response->assertOk()
            ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
            ->assertSee('<urlset', false)
            ->assertSee(route('universities.list'), false)
            ->assertSee(route('contact-us'), false);
    }
}
