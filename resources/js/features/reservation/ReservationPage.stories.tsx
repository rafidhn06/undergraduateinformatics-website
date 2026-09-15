import type { Story, StoryDefault } from '@ladle/react';
import { msw } from '@ladle/react';

import { type MsFormPayload } from '../../types/ms-forms';
import { ReservationPage } from './ReservationPage';
import { ReservationSkeleton } from './ReservationStates';

const reservationPayload: MsFormPayload = {
    link: 'https://forms.office.com/r/reservation123',
    title: { text: 'Reservasi Pertemuan dengan Prodi' },
    description: {
        text: 'Ajukan reservasi pertemuan dengan Program Studi Sarjana Informatika Telkom University.',
    },
    reservation: {
        dateQuestionId: 'tanggal',
        shiftQuestionId: 'sesi',
        allowedDays: [1, 2, 4, 5],
    },
    sections: [
        {
            id: 'section-1',
            title: null,
            subtitle: { text: 'Semua pertanyaan bertanda * wajib diisi.' },
            questionIds: ['jenis'],
        },
    ],
    questions: [
        {
            id: 'jenis',
            title: { text: 'Jenis Pertemuan' },
            subtitle: null,
            type: 'choice',
            required: true,
            multiple: false,
            choices: [
                { value: 'Konsultasi', label: { text: 'Konsultasi' }, branchTargetId: null },
                { value: 'Bimbingan', label: { text: 'Bimbingan' }, branchTargetId: null },
                { value: 'Lainnya', label: { text: 'Lainnya' }, branchTargetId: null },
            ],
        },
    ],
};

const formHandlers = [
    msw.http.get('/api/reservation', () =>
        msw.HttpResponse.json({ status: 'success', data: reservationPayload })
    ),
    msw.http.post('/api/reservation', () => msw.HttpResponse.json({ success: true })),
];

export default {
    title: 'Reservasi',
} satisfies StoryDefault;

export const Form: Story = () => <ReservationPage />;
Form.msw = formHandlers;

export const Loading: Story = () => <ReservationSkeleton />;
