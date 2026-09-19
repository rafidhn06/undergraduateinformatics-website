import { QueryClient, QueryClientProvider } from '@tanstack/react-query';

import { render, screen, waitFor } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import axios, { AxiosError, type AxiosResponse } from 'axios';
import { beforeEach, describe, expect, it, vi } from 'vitest';

import { type MsFormPayload } from '@/types/ms-forms';

import { ReservationForm } from './ReservationForm';

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

const reservationPayload: MsFormPayload = {
    link: 'https://forms.office.com/r/reservation123',
    title: { text: 'Reservasi Pertemuan dengan Prodi' },
    description: null,
    reservation: {
        dateQuestionId: 'tanggal',
        shiftQuestionId: 'sesi',
        allowedDays: [1, 2, 4, 5],
    },
    sections: [
        {
            id: 'section-1',
            title: null,
            subtitle: null,
            questionIds: ['nama', 'tanggal', 'sesi'],
        },
    ],
    questions: [
        {
            id: 'nama',
            title: { text: 'Nama Lengkap' },
            subtitle: null,
            type: 'text',
            required: true,
            multiple: false,
            choices: [],
        },
        {
            id: 'tanggal',
            title: { text: 'Tanggal Pertemuan' },
            subtitle: null,
            type: 'date',
            required: true,
            multiple: false,
            choices: [],
        },
        {
            id: 'sesi',
            title: { text: 'Sesi Pertemuan' },
            subtitle: null,
            type: 'choice',
            required: true,
            multiple: false,
            choices: [
                { value: '09:00', label: { text: '09:00' }, branchTargetId: null },
                { value: '13:00', label: { text: '13:00' }, branchTargetId: null },
            ],
        },
    ],
};

function renderForm() {
    const queryClient = new QueryClient({
        defaultOptions: {
            queries: { retry: false },
        },
    });

    return render(
        <QueryClientProvider client={queryClient}>
            <ReservationForm
                title={reservationPayload.title}
                description={reservationPayload.description}
                sections={reservationPayload.sections}
                questions={reservationPayload.questions}
                submitUrl="/api/reservation-submissions"
                reservation={reservationPayload.reservation!}
            />
        </QueryClientProvider>
    );
}

describe('ReservationForm', () => {
    beforeEach(() => {
        vi.mocked(axios.get).mockResolvedValue({
            data: { status: 'success', data: { available: true } },
        });
        vi.mocked(axios.post).mockResolvedValue({
            data: { status: 'success', message: 'Reservation submitted successfully.' },
        });
    });

    it('shows an allowed-day error for a disallowed date', async () => {
        renderForm();

        await screen.findByRole('heading', { name: 'Reservasi Pertemuan dengan Prodi' });
        await userEvent.type(screen.getByLabelText(/Nama Lengkap/), 'Budi');
        await userEvent.type(screen.getByLabelText(/Tanggal Pertemuan/), '2026-09-09');
        await userEvent.click(screen.getByRole('radio', { name: '09:00' }));

        expect(
            await screen.findByText(
                'Reservasi hanya dapat dilakukan di hari Senin, Selasa, Kamis, dan Jumat.'
            )
        ).toBeInTheDocument();
        expect(axios.post).not.toHaveBeenCalled();
    });

    it('shows the slot-full message under the shift field and scrolls only on submit', async () => {
        const scrollIntoView = vi.mocked(Element.prototype.scrollIntoView);
        scrollIntoView.mockClear();

        vi.mocked(axios.get).mockImplementation((url) => {
            if (url === '/api/reservation-form/availability') {
                return Promise.resolve({
                    data: { status: 'success', data: { available: false } },
                });
            }
            return Promise.resolve({ data: { status: 'success', data: { available: true } } });
        });

        renderForm();

        await screen.findByRole('heading', { name: 'Reservasi Pertemuan dengan Prodi' });
        await userEvent.type(screen.getByLabelText(/Nama Lengkap/), 'Budi');
        await userEvent.type(screen.getByLabelText(/Tanggal Pertemuan/), '2026-09-08');
        await userEvent.click(screen.getByRole('radio', { name: '09:00' }));

        expect(
            await screen.findByText('Jadwal pada tanggal dan sesi ini sudah terisi.')
        ).toBeInTheDocument();
        expect(screen.getByRole('button', { name: /Kirim/ })).toBeEnabled();
        expect(scrollIntoView).not.toHaveBeenCalled();

        await userEvent.click(screen.getByRole('button', { name: /Kirim/ }));

        expect(scrollIntoView).toHaveBeenCalled();
    });

    it('shows the server field error message in Indonesian and scrolls to the field on a rejected submit', async () => {
        const scrollIntoView = vi.mocked(Element.prototype.scrollIntoView);
        scrollIntoView.mockClear();

        const error = new AxiosError('Request failed');
        error.response = {
            status: 422,
            data: {
                errors: {
                    shift: ['The selected session is not available.'],
                },
            },
        } as AxiosResponse;
        vi.mocked(axios.post).mockRejectedValue(error);

        renderForm();

        await screen.findByRole('heading', { name: 'Reservasi Pertemuan dengan Prodi' });
        await userEvent.type(screen.getByLabelText(/Nama Lengkap/), 'Budi');
        await userEvent.type(screen.getByLabelText(/Tanggal Pertemuan/), '2026-09-08');
        await userEvent.click(screen.getByRole('radio', { name: '09:00' }));
        await userEvent.click(screen.getByRole('button', { name: /Kirim/ }));

        expect(await screen.findByText('Sesi yang dipilih tidak tersedia.')).toBeInTheDocument();
        expect(scrollIntoView).toHaveBeenCalled();
    });

    it('clears the previous server error when the user changes the shift', async () => {
        const error = new AxiosError('Request failed');
        error.response = {
            status: 422,
            data: {
                errors: {
                    shift: ['The selected session is not available.'],
                },
            },
        } as AxiosResponse;
        vi.mocked(axios.post).mockRejectedValue(error);

        renderForm();

        await screen.findByRole('heading', { name: 'Reservasi Pertemuan dengan Prodi' });
        await userEvent.type(screen.getByLabelText(/Nama Lengkap/), 'Budi');
        await userEvent.type(screen.getByLabelText(/Tanggal Pertemuan/), '2026-09-08');
        await userEvent.click(screen.getByRole('radio', { name: '09:00' }));
        await userEvent.click(screen.getByRole('button', { name: /Kirim/ }));

        expect(await screen.findByText('Sesi yang dipilih tidak tersedia.')).toBeInTheDocument();

        await userEvent.click(screen.getByRole('radio', { name: '13:00' }));

        expect(screen.queryByText('Sesi yang dipilih tidak tersedia.')).not.toBeInTheDocument();
    });

    it('submits normally when the slot is available', async () => {
        renderForm();

        await screen.findByRole('heading', { name: 'Reservasi Pertemuan dengan Prodi' });
        await userEvent.type(screen.getByLabelText(/Nama Lengkap/), 'Budi');
        await userEvent.type(screen.getByLabelText(/Tanggal Pertemuan/), '2026-09-08');
        await userEvent.click(screen.getByRole('radio', { name: '09:00' }));
        await userEvent.click(screen.getByRole('button', { name: /Kirim/ }));

        await waitFor(() => {
            expect(axios.post).toHaveBeenCalledWith('/api/reservation-submissions', {
                answers: [
                    { questionId: 'nama', answer: 'Budi' },
                    { questionId: 'tanggal', answer: '2026-09-08' },
                    { questionId: 'sesi', answer: '09:00' },
                ],
            });
        });
    });
});
