<?php

namespace Tests\Feature;

use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ExampleTest extends TestCase
{
    #[Test]
    public function root_redirects_to_candidaturas_create(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('candidaturas.create'));

        $this->followingRedirects()
            ->get('/')
            ->assertOk();
    }
}