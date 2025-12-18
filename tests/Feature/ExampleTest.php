<?php

<<<<<<< HEAD
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
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
=======
test('returns a successful response', function () {
    $response = $this->get(route('home'));

    $response->assertStatus(200);
});
>>>>>>> 2a1ae96026e2911a0042270c12e071e9bbd663d3
