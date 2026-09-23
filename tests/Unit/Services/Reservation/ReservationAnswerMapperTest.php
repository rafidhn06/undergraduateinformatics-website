<?php

namespace Tests\Unit\Services\Reservation;

use App\Services\Reservation\ReservationAnswerMapper;
use App\Services\Reservation\ReservationMappingException;
use Tests\TestCase;

class ReservationAnswerMapperTest extends TestCase
{
    private array $mapping = [
        'r10000000000000000000000000000001' => 'date',
        'r10000000000000000000000000000002' => 'shift',
        'r10000000000000000000000000000003' => 'requested_by',
        'r10000000000000000000000000000004' => 'meeting_room',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'reservation.form_mapping' => $this->mapping,
            'reservation.required_fields' => ['date', 'shift', 'requested_by'],
        ]);
    }

    public function test_it_maps_answers_to_columns_by_question_id(): void
    {
        $attributes = app(ReservationAnswerMapper::class)->map([
            ['questionId' => 'r10000000000000000000000000000001', 'answer' => '2026-09-10'],
            ['questionId' => 'r10000000000000000000000000000002', 'answer' => '09:00'],
            ['questionId' => 'r10000000000000000000000000000003', 'answer' => 'Budi'],
            ['questionId' => 'r10000000000000000000000000000004', 'answer' => 'Ruang 101'],
        ]);

        $this->assertSame([
            'date' => '2026-09-10',
            'shift' => '09:00:00',
            'requested_by' => 'Budi',
            'meeting_room' => 'Ruang 101',
        ], $attributes);
    }

    public function test_it_parses_an_iso_datetime_value_for_the_date_column(): void
    {
        $attributes = app(ReservationAnswerMapper::class)->map([
            ['questionId' => 'r10000000000000000000000000000001', 'answer' => '2026-09-10T00:00:00.000Z'],
            ['questionId' => 'r10000000000000000000000000000002', 'answer' => '09:00'],
            ['questionId' => 'r10000000000000000000000000000003', 'answer' => 'Budi'],
        ]);

        $this->assertSame('2026-09-10', $attributes['date']);
    }

    public function test_it_ignores_answers_with_unmapped_question_ids(): void
    {
        $attributes = app(ReservationAnswerMapper::class)->map([
            ['questionId' => 'r10000000000000000000000000000001', 'answer' => '2026-09-10'],
            ['questionId' => 'r10000000000000000000000000000002', 'answer' => '09:00'],
            ['questionId' => 'r10000000000000000000000000000003', 'answer' => 'Budi'],
            ['questionId' => 'r99999999999999999999999999999999', 'answer' => 'ignored'],
        ]);

        $this->assertCount(3, $attributes);
    }

    public function test_it_throws_when_a_required_field_is_missing(): void
    {
        try {
            app(ReservationAnswerMapper::class)->map([
                ['questionId' => 'r10000000000000000000000000000001', 'answer' => '2026-09-10'],
                ['questionId' => 'r10000000000000000000000000000003', 'answer' => 'Budi'],
            ]);

            $this->fail('Expected ReservationMappingException was not thrown.');
        } catch (ReservationMappingException $e) {
            $this->assertContains('shift', $e->fields);
        }
    }

    public function test_it_joins_array_answers_for_text_columns(): void
    {
        $attributes = app(ReservationAnswerMapper::class)->map([
            ['questionId' => 'r10000000000000000000000000000001', 'answer' => '2026-09-10'],
            ['questionId' => 'r10000000000000000000000000000002', 'answer' => '09:00'],
            ['questionId' => 'r10000000000000000000000000000003', 'answer' => 'Budi'],
            ['questionId' => 'r10000000000000000000000000000004', 'answer' => ['Ruang 101', 'Ruang 102']],
        ]);

        $this->assertSame('Ruang 101, Ruang 102', $attributes['meeting_room']);
    }

    public function test_it_rejects_an_invalid_date_value(): void
    {
        try {
            app(ReservationAnswerMapper::class)->map([
                ['questionId' => 'r10000000000000000000000000000001', 'answer' => 'not-a-date'],
                ['questionId' => 'r10000000000000000000000000000002', 'answer' => '09:00'],
                ['questionId' => 'r10000000000000000000000000000003', 'answer' => 'Budi'],
            ]);

            $this->fail('Expected ReservationMappingException was not thrown.');
        } catch (ReservationMappingException $e) {
            $this->assertArrayHasKey('date', $e->fieldErrors);
        }
    }
}
