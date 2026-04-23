<?php

namespace Tests\Feature;

use Tests\TestCase;
use Thenextweb\Definitions\Dictionary\Field;
use Thenextweb\Definitions\Generic;

class DefinitionTest extends TestCase
{
    public function test_builds_valid_generic_definition(): void
    {
        $definition = (new Generic())
            ->setDescription('Test pass')
            ->setSerialNumber('serial-001')
            ->addPrimaryField(new Field('title', 'Hello'))
            ->getPassDefinition();

        $this->assertSame('Test pass', $definition['description']);
        $this->assertSame('pass.com.example.app', $definition['passTypeIdentifier']);
        $this->assertSame('serial-001', $definition['serialNumber']);
        $this->assertSame('title', $definition['generic']['primaryFields'][0]['key']);
    }

    public function test_validation_fails_without_required_fields(): void
    {
        $this->expectException(\Illuminate\Validation\ValidationException::class);

        (new Generic())->getPassDefinition();
    }
}