<?php

namespace Tests\Unit;

use App\Exceptions\BusinessRuleViolation;
use App\Services\PostService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_title_must_be_unique_per_day(): void
    {
        $service = new PostService();

        $payload = [
            'title' => 'Same Title',
            'body' => str_repeat('Body ', 15),
            'status' => 'published',
        ];

        $service->create($payload);

        $this->expectException(BusinessRuleViolation::class);
    $this->expectExceptionMessage('Ya existe un post con este título hoy.');

        $service->create($payload);
    }
}
