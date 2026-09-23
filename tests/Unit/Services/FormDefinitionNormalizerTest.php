<?php

namespace Tests\Unit\Services;

use App\Services\MsForms\FormDefinitionNormalizer;
use PHPUnit\Framework\TestCase;

final class FormDefinitionNormalizerTest extends TestCase
{
    public function test_normalizes_sections_and_branching(): void
    {
        $raw = json_decode(
            file_get_contents(__DIR__.'/../../Fixtures/msforms/form-definition-branching.raw.json'),
            true
        );

        $result = (new FormDefinitionNormalizer)->normalize($raw);

        $this->assertSame([
            [
                'id' => 'p1',
                'title' => ['text' => 'Kerahasiaan Identitas'],
                'subtitle' => ['text' => 'Pilih cara Anda menyampaikan masukan.'],
                'questionIds' => ['q1'],
            ],
            [
                'id' => 'p2',
                'title' => ['text' => 'IDENTITAS'],
                'subtitle' => ['text' => 'Isi identitas agar dapat ditindaklanjuti.'],
                'questionIds' => ['q2'],
            ],
            [
                'id' => 'p3',
                'title' => ['text' => 'PENGADUAN'],
                'subtitle' => ['text' => 'Sampaikan masukan Anda di sini.'],
                'questionIds' => ['q3'],
            ],
        ], $result['sections']);

        $q1 = $result['questions'][0];
        $this->assertSame('end', $q1['choices'][0]['branchTargetId']);
        $this->assertSame('q2', $q1['choices'][1]['branchTargetId']);

        $q3 = $result['questions'][2];
        $this->assertNull($q3['choices'][0]['branchTargetId']);
        $this->assertArrayNotHasKey('branchInfo', $q3['choices'][0]);
    }

    public function test_normalizes_genuine_form_definition(): void
    {
        $raw = json_decode(
            file_get_contents(__DIR__.'/../../Fixtures/msforms/form-definition.raw.json'),
            true
        );

        $result = (new FormDefinitionNormalizer)->normalize($raw);

        $this->assertSame([
            [
                'id' => 'r5ea034e6b67a462ba2a1ff857fad2490',
                'title' => ['text' => 'this is section title'],
                'subtitle' => ['text' => 'this is section subtitle'],
                'questionIds' => ['rd7645a06d5f94664917ff0617f123de3'],
            ],
            [
                'id' => 'r988954e7af514c56b64d4d9fc107b58d',
                'title' => ['text' => 'this is the next section title that doesnt have subtitle'],
                'subtitle' => null,
                'questionIds' => ['rb38b17fd578e4dfbb6b32d32f4dfc885'],
            ],
            [
                'id' => 'rb0f93bfc937f4ae299b422a9b623e280',
                'title' => null,
                'subtitle' => null,
                'questionIds' => ['r729590f423cd4629a005ea35c177fa59'],
            ],
        ], $result['sections']);

        $this->assertSame('text', $result['questions'][0]['type']);
        $this->assertSame('date', $result['questions'][1]['type']);
        $this->assertSame('choice', $result['questions'][2]['type']);

        $choice = $result['questions'][2];
        $this->assertSame(['Option 1', 'Option 2'], array_column($choice['choices'], 'value'));
        $this->assertSame(['text' => 'Option 1'], $choice['choices'][0]['label']);
        $this->assertSame(['text' => 'Option 2'], $choice['choices'][1]['label']);
        $this->assertNull($choice['choices'][0]['branchTargetId']);
        $this->assertArrayNotHasKey('branchInfo', $choice['choices'][0]);
    }

    public function test_single_section_when_no_section_info(): void
    {
        $raw = [
            'title' => 'T',
            'questions' => [
                ['id' => 'a', 'type' => 'Question.TextField', 'title' => 'A', 'questionInfo' => null],
            ],
        ];

        $result = (new FormDefinitionNormalizer)->normalize($raw);

        $this->assertSame(['a'], $result['sections'][0]['questionIds']);
    }

    public function test_empty_section_titles_and_subtitles_normalize_to_null(): void
    {
        $raw = [
            'title' => 'T',
            'description' => null,
            'descriptiveQuestions' => [
                ['id' => 'p1', 'type' => 'Question.ColumnGroup', 'title' => '', 'subtitle' => '', 'order' => 1],
                ['id' => 'p2', 'type' => 'Question.ColumnGroup', 'title' => null, 'subtitle' => null, 'order' => 2],
            ],
            'questions' => [],
        ];

        $result = (new FormDefinitionNormalizer)->normalize($raw);

        $this->assertNull($result['sections'][0]['title']);
        $this->assertNull($result['sections'][0]['subtitle']);
        $this->assertNull($result['sections'][1]['title']);
        $this->assertNull($result['sections'][1]['subtitle']);
        $this->assertSame([], $result['sections'][0]['questionIds']);
    }

    public function test_normalizes_rich_text_fields(): void
    {
        $raw = json_decode(
            file_get_contents(__DIR__.'/../../Fixtures/msforms/form-definition-richtext.raw.json'),
            true
        );

        $result = (new FormDefinitionNormalizer)->normalize($raw);

        $this->assertSame(
            ['text' => 'Form Umpan Balik', 'html' => 'Form <b>Umpan</b> Balik'],
            $result['title']
        );
        $this->assertSame(
            ['text' => 'Bantu kami meningkatkan layanan.', 'html' => 'Bantu kami <i>meningkatkan</i> layanan.'],
            $result['description']
        );
        $this->assertSame(
            ['text' => 'Section Judul', 'html' => 'Section <u>Judul</u>'],
            $result['sections'][0]['title']
        );
        $this->assertSame(
            ['text' => 'Subtitle section', 'html' => 'Subtitle <b>section</b>'],
            $result['sections'][0]['subtitle']
        );
        $this->assertSame(
            ['text' => 'Pilih satu', 'html' => 'Pilih <b>satu</b>'],
            $result['questions'][0]['title']
        );
        $this->assertNull($result['questions'][0]['subtitle']);
        $this->assertSame(
            ['text' => 'Opsi 1', 'html' => '<b>Opsi 1</b>'],
            $result['questions'][0]['choices'][0]['label']
        );
        $this->assertSame(['text' => 'Opsi 2'], $result['questions'][0]['choices'][1]['label']);
        $this->assertSame('<b>Opsi 1</b>', $result['questions'][0]['choices'][0]['value']);
    }

    public function test_falls_back_to_plain_text_when_rich_source_missing(): void
    {
        $raw = [
            'title' => 'T',
            'questions' => [
                ['id' => 'a', 'type' => 'Question.TextField', 'title' => 'A', 'questionInfo' => '{}'],
            ],
        ];

        $result = (new FormDefinitionNormalizer)->normalize($raw);

        $this->assertSame(['text' => 'T'], $result['title']);
        $this->assertNull($result['description']);
        $this->assertSame(['text' => 'A'], $result['questions'][0]['title']);
        $this->assertNull($result['questions'][0]['subtitle']);
    }

    public function test_does_not_append_other_choice_when_allow_other_answer_is_enabled(): void
    {
        $raw = [
            'title' => 'T',
            'questions' => [
                [
                    'id' => 'q1',
                    'type' => 'Question.Choice',
                    'title' => 'Waktu Pertemuan',
                    'required' => true,
                    'allowMultipleValues' => false,
                    'questionInfo' => json_encode([
                        'Choices' => [
                            ['Description' => '09:00'],
                            ['Description' => '13:00'],
                        ],
                        'AllowOtherAnswer' => true,
                    ]),
                ],
            ],
        ];

        $result = (new FormDefinitionNormalizer)->normalize($raw);

        $this->assertSame(['09:00', '13:00'], array_column($result['questions'][0]['choices'], 'value'));
    }
}
