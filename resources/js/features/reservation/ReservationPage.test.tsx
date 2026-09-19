import { Suspense } from 'react';

import { QueryClient, QueryClientProvider } from '@tanstack/react-query';

import { render, screen, waitFor } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import axios, { AxiosError, type AxiosResponse } from 'axios';
import { beforeEach, describe, expect, it, vi } from 'vitest';

import { type MsFormPayload } from '@/types/ms-forms';

import { ReservationPage } from './ReservationPage';

vi.mock('axios', async () => {
    const actual = await vi.importActual<typeof import('axios')>('axios');

    return {
        ...actual,
        default: {
            ...actual.default,
            get: vi.fn(),
            post: vi.fn(),
        },
    };
});

function axiosError(status: number, message?: string) {
    const error = new AxiosError(message ?? 'Request failed');
    error.response = {
        status,
        data: message ? { message } : {},
    } as AxiosResponse;
    return error;
}

const formPayload: MsFormPayload = {
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
            questionIds: ['jenis', 'nama'],
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
            ],
        },
        {
            id: 'nama',
            title: { text: 'Nama Lengkap' },
            subtitle: null,
            type: 'text',
            required: true,
            multiple: false,
            choices: [],
        },
    ],
};

function renderSection() {
    const queryClient = new QueryClient({
        defaultOptions: {
            queries: {
                retry: false,
            },
        },
    });

    return render(
        <QueryClientProvider client={queryClient}>
            <Suspense fallback={null}>
                <ReservationPage />
            </Suspense>
        </QueryClientProvider>
    );
}

describe('ReservationPage', () => {
    beforeEach(() => {
        vi.mocked(axios.get).mockResolvedValue({
            data: { status: 'success', data: formPayload },
        });
        vi.mocked(axios.post).mockResolvedValue({
            data: { status: 'success', message: 'Reservation submitted successfully.' },
        });
        delete (window as any).__INITIAL_DATA__;
    });

    it('renders the form title, description, and questions', async () => {
        renderSection();

        expect(await screen.findByText('Reservasi Pertemuan dengan Prodi')).toBeInTheDocument();
        expect(
            screen.getByText(
                'Ajukan reservasi pertemuan dengan Program Studi Sarjana Informatika Telkom University.'
            )
        ).toBeInTheDocument();
        expect(screen.getByRole('heading', { name: 'Jenis Pertemuan' })).toBeInTheDocument();
        expect(screen.getByRole('heading', { name: 'Nama Lengkap' })).toBeInTheDocument();
    });

    it('requires answers for required questions before submitting', async () => {
        renderSection();

        await screen.findByText('Reservasi Pertemuan dengan Prodi');
        await userEvent.click(screen.getByRole('button', { name: /Kirim/ }));

        expect(await screen.findAllByText('Pertanyaan ini wajib diisi')).toHaveLength(2);
        expect(axios.post).not.toHaveBeenCalled();
    });

    it('submits the answers to the reservation endpoint', async () => {
        renderSection();

        await screen.findByText('Reservasi Pertemuan dengan Prodi');
        await userEvent.click(screen.getByRole('radio', { name: 'Konsultasi' }));
        await userEvent.type(screen.getByLabelText(/Nama Lengkap/), 'Budi');
        await userEvent.click(screen.getByRole('button', { name: /Kirim/ }));

        await waitFor(() => {
            expect(axios.post).toHaveBeenCalledWith('/api/reservation-submissions', {
                answers: [
                    { questionId: 'jenis', answer: 'Konsultasi' },
                    { questionId: 'nama', answer: 'Budi' },
                ],
            });
        });
    });

    it('shows a confirmation after a successful submit', async () => {
        renderSection();

        await screen.findByText('Reservasi Pertemuan dengan Prodi');
        await userEvent.click(screen.getByRole('radio', { name: 'Bimbingan' }));
        await userEvent.type(screen.getByLabelText(/Nama Lengkap/), 'Budi');
        await userEvent.click(screen.getByRole('button', { name: /Kirim/ }));

        expect(await screen.findByText('Terima kasih!')).toBeInTheDocument();
        expect(screen.getByText('Formulir Anda telah berhasil dikirim.')).toBeInTheDocument();
    });

    it('shows an error message when submitting fails', async () => {
        vi.mocked(axios.post).mockRejectedValue(axiosError(422, 'The schedule is already full.'));

        renderSection();

        await screen.findByText('Reservasi Pertemuan dengan Prodi');
        await userEvent.click(screen.getByRole('radio', { name: 'Konsultasi' }));
        await userEvent.type(screen.getByLabelText(/Nama Lengkap/), 'Budi');
        await userEvent.click(screen.getByRole('button', { name: /Kirim/ }));

        expect(
            await screen.findByText('Gagal mengirim jawaban. Silakan coba beberapa saat lagi.')
        ).toBeInTheDocument();
    });

    it('shows its own message when the form is unavailable on submit', async () => {
        vi.mocked(axios.post).mockRejectedValue(
            axiosError(404, 'Reservation form is unavailable.')
        );

        renderSection();

        await screen.findByText('Reservasi Pertemuan dengan Prodi');
        await userEvent.click(screen.getByRole('radio', { name: 'Konsultasi' }));
        await userEvent.type(screen.getByLabelText(/Nama Lengkap/), 'Budi');
        await userEvent.click(screen.getByRole('button', { name: /Kirim/ }));

        expect(await screen.findByText('Formulir sedang tidak tersedia.')).toBeInTheDocument();
    });

    it('shows a message when the link is set but questions are unavailable', async () => {
        vi.mocked(axios.get).mockResolvedValue({
            data: {
                status: 'success',
                data: { link: 'https://forms.office.com/r/reservation123' },
            },
        });

        renderSection();

        expect(await screen.findByText(/Formulir sedang tidak tersedia/)).toBeInTheDocument();
        expect(screen.queryByText('Reservasi Pertemuan dengan Prodi')).not.toBeInTheDocument();
    });
});
