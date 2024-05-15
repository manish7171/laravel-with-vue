<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RouteTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function testApplicationReturnsASuccessfulResponse(): void
    {
        $response = $this->get('/frontend');

        $response->assertStatus(200);
    }

    public function testApplicationShouldRedirectToFrontendRoute(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/frontend');
    }
}
