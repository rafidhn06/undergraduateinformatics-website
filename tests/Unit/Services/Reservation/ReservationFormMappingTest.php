<?php

namespace Tests\Unit\Services\Reservation;

use App\Services\Reservation\ReservationFormMapping;
use Tests\TestCase;

class ReservationFormMappingTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'reservation.form_mapping' => [
                'q-date' => 'date',
                'q-shift' => 'shift',
                'q-name' => 'requested_by',
            ],
            'reservation.required_fields' => ['date', 'shift', 'requested_by'],
        ]);
    }

    public function test_it_resolves_question_ids_in_both_directions(): void
    {
        $mapping = app(ReservationFormMapping::class);

        $this->assertSame('q-date', $mapping->questionIdFor('date'));
        $this->assertSame('date', $mapping->fieldFor('q-date'));
        $this->assertNull($mapping->questionIdFor('unknown'));
        $this->assertNull($mapping->fieldFor('unknown'));
    }

}
