<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        // The site itself is a separate frontend, so / sends visitors to Nova.
        $this->get('/')->assertRedirect('/admin/login');
    }
}
