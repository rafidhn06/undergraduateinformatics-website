import { Suspense } from 'react';

import { QueryClient, QueryClientProvider } from '@tanstack/react-query';

import { fireEvent, render, screen, waitFor } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import axios, { AxiosError, type AxiosResponse } from 'axios';
import { beforeEach, describe, expect, it, vi } from 'vitest';

import { type MsFormPayload } from '@/types/ms-forms';

import { FeedbackPage } from './FeedbackPage';

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
    link: 'https://forms.office.com/r/abc123',
    title: { text: 'Form Umpan Balik Test' },
    description: { text: 'Deskripsi formulir pengujian' },
    sections: [
        {
            id: 'section-1',
            title: null,
            subtitle: { text: 'Pilih cara menyampaikan masukan.' },
            questionIds: ['r1'],
        },
        { id: 'section-2', title: null, subtitle: null, questionIds: ['r2'] },
        { id: 'section-3', title: null, subtitle: null, questionIds: ['r3'] },
    ],
    questions: [
        {
            id: 'r1',
            title: { text: 'Rahasiakan identitas?' },
            subtitle: null,
            type: 'choice',
            required: true,
            multiple: false,
            choices: [
                { value: 'Ya', label: { text: 'Ya' }, branchTargetId: 'r3' },
                { value: 'Tidak', label: { text: 'Tidak' }, branchTargetId: 'r2' },
            ],
        },
        {
            id: 'r2',
            title: { text: 'Nama Lengkap' },
            subtitle: null,
            type: 'text',
            required: true,
            multiple: false,
            choices: [],
        },
        {
            id: 'r3',
            title: { text: 'Jenis Masukan' },
            subtitle: { text: 'Pilih salah satu' },
            type: 'choice',
            required: true,
            multiple: false,
            choices: [
                { value: 'Saran', label: { text: 'Saran' }, branchTargetId: null },
                { value: 'Keluhan', label: { text: 'Keluhan' }, branchTargetId: null },
            ],
        },
    ],
};

const formWithExtraTypes: MsFormPayload = {
    link: 'https://forms.office.com/r/abc123',
    title: { text: 'Form Tipe Lengkap' },
    description: null,
    sections: [
        {
            id: 'section-1',
            title: null,
            subtitle: null,
            questionIds: ['q1', 'q2', 'q3'],
        },
    ],
    questions: [
        {
            id: 'q1',
            title: { text: 'Pilih opsi' },
            subtitle: null,
            type: 'choice',
            required: true,
            multiple: true,
            choices: [
                { value: 'A', label: { text: 'A' }, branchTargetId: null },
                { value: 'B', label: { text: 'B' }, branchTargetId: null },
            ],
        },
        {
            id: 'q2',
            title: { text: 'Tanggal Kejadian' },
            subtitle: null,
            type: 'date',
            required: true,
            multiple: false,
            choices: [],
        },
        {
            id: 'q3',
            title: { text: 'Catatan' },
            subtitle: null,
            type: 'text',
            required: false,
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
                <FeedbackPage />
            </Suspense>
        </QueryClientProvider>
    );
}

describe('FeedbackPage', () => {
    beforeEach(() => {
        vi.mocked(axios.get).mockResolvedValue({
            data: { status: 'success', data: formPayload },
        });
        vi.mocked(axios.post).mockResolvedValue({
            data: { status: 'success', message: 'Feedback submitted successfully.' },
        });
        delete (window as any).__INITIAL_DATA__;
    });

    it('renders the form title, description, and first section questions', async () => {
        renderSection();

        expect(await screen.findByText('Form Umpan Balik Test')).toBeInTheDocument();
        expect(screen.getByText('Deskripsi formulir pengujian')).toBeInTheDocument();
        expect(screen.getByText('Pilih cara menyampaikan masukan.')).toBeInTheDocument();
        expect(screen.getByRole('heading', { name: 'Rahasiakan identitas?' })).toBeInTheDocument();
        expect(screen.queryByText('Nama Lengkap')).not.toBeInTheDocument();
        expect(screen.queryByText('Jenis Masukan')).not.toBeInTheDocument();
    });

    it('requires answers for visible required questions before advancing', async () => {
        renderSection();

        await screen.findByText('Form Umpan Balik Test');
        await userEvent.click(screen.getByRole('button', { name: /Lanjut/ }));

        expect(await screen.findAllByText(/wajib diisi/)).toHaveLength(1);
        expect(axios.post).not.toHaveBeenCalled();
    });

    it('deselects a choice when clicking the selected radio again', async () => {
        renderSection();

        await screen.findByText('Form Umpan Balik Test');
        const secretOption = screen.getByRole('radio', { name: 'Ya' });

        await userEvent.click(secretOption);
        expect(secretOption).toBeChecked();

        await userEvent.click(secretOption);
        expect(secretOption).not.toBeChecked();

        await userEvent.click(screen.getByRole('button', { name: /Lanjut/ }));

        expect(await screen.findAllByText(/wajib diisi/)).toHaveLength(1);
        expect(axios.post).not.toHaveBeenCalled();
    });

    it('deselects a choice when clicking anywhere on its card', async () => {
        renderSection();

        await screen.findByText('Form Umpan Balik Test');
        const secretOption = screen.getByRole('radio', { name: 'Ya' });

        await userEvent.click(secretOption);
        expect(secretOption).toBeChecked();

        const card = secretOption.closest('label');
        expect(card).not.toBeNull();
        await userEvent.click(card as HTMLLabelElement);
        expect(secretOption).not.toBeChecked();
    });

    it('skips the identity section and submits only visible answers when identity is secret', async () => {
        renderSection();

        await screen.findByText('Form Umpan Balik Test');
        await userEvent.click(screen.getByRole('radio', { name: 'Ya' }));
        await userEvent.click(screen.getByRole('button', { name: /Lanjut/ }));

        expect(await screen.findByRole('heading', { name: 'Jenis Masukan' })).toBeInTheDocument();
        expect(screen.queryByText('Nama Lengkap')).not.toBeInTheDocument();

        await userEvent.click(screen.getByRole('radio', { name: 'Saran' }));
        await userEvent.click(screen.getByRole('button', { name: /Kirim/ }));

        await waitFor(() => {
            expect(axios.post).toHaveBeenCalledWith('/api/feedback-submissions', {
                answers: [
                    { questionId: 'r1', answer: 'Ya' },
                    { questionId: 'r3', answer: 'Saran' },
                ],
            });
        });
    });

    it('branches to the identity section when identity is public', async () => {
        renderSection();

        await screen.findByText('Form Umpan Balik Test');
        await userEvent.click(screen.getByRole('radio', { name: 'Tidak' }));
        await userEvent.click(screen.getByRole('button', { name: /Lanjut/ }));

        expect(await screen.findByLabelText(/Nama Lengkap/)).toBeInTheDocument();
        await userEvent.type(screen.getByLabelText(/Nama Lengkap/), 'Budi');
        await userEvent.click(screen.getByRole('button', { name: /Lanjut/ }));

        await screen.findByRole('heading', { name: 'Jenis Masukan' });
        await userEvent.click(screen.getByRole('radio', { name: 'Saran' }));
        await userEvent.click(screen.getByRole('button', { name: /Kirim/ }));

        await waitFor(() => {
            expect(axios.post).toHaveBeenCalledWith('/api/feedback-submissions', {
                answers: [
                    { questionId: 'r1', answer: 'Tidak' },
                    { questionId: 'r2', answer: 'Budi' },
                    { questionId: 'r3', answer: 'Saran' },
                ],
            });
        });
    });

    it('returns to the branch section when navigating back across a skipped section', async () => {
        renderSection();

        await screen.findByText('Form Umpan Balik Test');
        await userEvent.click(screen.getByRole('radio', { name: 'Ya' }));
        await userEvent.click(screen.getByRole('button', { name: /Lanjut/ }));

        await screen.findByRole('heading', { name: 'Jenis Masukan' });
        await userEvent.click(screen.getByRole('button', { name: /Kembali/ }));

        expect(screen.getByRole('radio', { name: 'Ya' })).toBeInTheDocument();
        expect(screen.queryByText('Nama Lengkap')).not.toBeInTheDocument();
    });

    it('clears identity answers that become hidden after changing the branch answer', async () => {
        renderSection();

        await screen.findByText('Form Umpan Balik Test');
        await userEvent.click(screen.getByRole('radio', { name: 'Tidak' }));
        await userEvent.click(screen.getByRole('button', { name: /Lanjut/ }));

        await screen.findByLabelText(/Nama Lengkap/);
        await userEvent.type(screen.getByLabelText(/Nama Lengkap/), 'Budi');
        await userEvent.click(screen.getByRole('button', { name: /Lanjut/ }));

        await screen.findByRole('heading', { name: 'Jenis Masukan' });
        await userEvent.click(screen.getByRole('button', { name: /Kembali/ }));
        await userEvent.click(screen.getByRole('button', { name: /Kembali/ }));

        await userEvent.click(screen.getByRole('radio', { name: 'Ya' }));
        await userEvent.click(screen.getByRole('button', { name: /Lanjut/ }));

        await screen.findByRole('heading', { name: 'Jenis Masukan' });
        await userEvent.click(screen.getByRole('radio', { name: 'Saran' }));
        await userEvent.click(screen.getByRole('button', { name: /Kirim/ }));

        await waitFor(() => {
            expect(axios.post).toHaveBeenCalledWith('/api/feedback-submissions', {
                answers: [
                    { questionId: 'r1', answer: 'Ya' },
                    { questionId: 'r3', answer: 'Saran' },
                ],
            });
        });
    });

    it('scrolls to the first question of the section when moving between sections', async () => {
        const scrollIntoView = vi.mocked(Element.prototype.scrollIntoView);
        scrollIntoView.mockClear();

        renderSection();

        await screen.findByText('Form Umpan Balik Test');
        await userEvent.click(screen.getByRole('radio', { name: 'Tidak' }));
        await userEvent.click(screen.getByRole('button', { name: /Lanjut/ }));

        expect(await screen.findByLabelText(/Nama Lengkap/)).toBeInTheDocument();
        expect(scrollIntoView).toHaveBeenCalled();

        scrollIntoView.mockClear();
        await userEvent.click(screen.getByRole('button', { name: /Kembali/ }));

        expect(screen.getByRole('radio', { name: 'Tidak' })).toBeInTheDocument();
        expect(scrollIntoView).toHaveBeenCalled();
    });

    it('shows an error message when submitting fails', async () => {
        vi.mocked(axios.post).mockRejectedValue(
            axiosError(422, 'Failed to submit the form. Please try again later.')
        );

        renderSection();

        await screen.findByText('Form Umpan Balik Test');
        await userEvent.click(screen.getByRole('radio', { name: 'Ya' }));
        await userEvent.click(screen.getByRole('button', { name: /Lanjut/ }));

        await screen.findByRole('heading', { name: 'Jenis Masukan' });
        await userEvent.click(screen.getByRole('radio', { name: 'Saran' }));
        await userEvent.click(screen.getByRole('button', { name: /Kirim/ }));

        expect(
            await screen.findByText('Gagal mengirim jawaban. Silakan coba beberapa saat lagi.')
        ).toBeInTheDocument();
    });

    it('shows its own message when the form is unavailable on submit', async () => {
        vi.mocked(axios.post).mockRejectedValue(axiosError(404, 'Feedback form is unavailable.'));

        renderSection();

        await screen.findByText('Form Umpan Balik Test');
        await userEvent.click(screen.getByRole('radio', { name: 'Ya' }));
        await userEvent.click(screen.getByRole('button', { name: /Lanjut/ }));

        await screen.findByRole('heading', { name: 'Jenis Masukan' });
        await userEvent.click(screen.getByRole('radio', { name: 'Saran' }));
        await userEvent.click(screen.getByRole('button', { name: /Kirim/ }));

        expect(await screen.findByText('Formulir sedang tidak tersedia.')).toBeInTheDocument();
    });

    it('shows a message when the link is set but questions are unavailable', async () => {
        vi.mocked(axios.get).mockResolvedValue({
            data: {
                status: 'success',
                data: { link: 'https://forms.office.com/r/abc123' },
            },
        });

        renderSection();

        expect(await screen.findByText(/Formulir sedang tidak tersedia/)).toBeInTheDocument();
        expect(screen.queryByText('Form Umpan Balik Test')).not.toBeInTheDocument();
    });

    it('shows a confirmation and allows filling the form again after a successful submit', async () => {
        renderSection();

        await screen.findByText('Form Umpan Balik Test');
        await userEvent.click(screen.getByRole('radio', { name: 'Ya' }));
        await userEvent.click(screen.getByRole('button', { name: /Lanjut/ }));
        await userEvent.click(screen.getByRole('radio', { name: 'Saran' }));
        await userEvent.click(screen.getByRole('button', { name: /Kirim/ }));

        expect(await screen.findByText('Terima kasih!')).toBeInTheDocument();
        expect(screen.getByText('Formulir Anda telah berhasil dikirim.')).toBeInTheDocument();

        await userEvent.click(screen.getByRole('button', { name: /Isi Formulir Lagi/ }));

        expect(
            await screen.findByRole('heading', { name: 'Rahasiakan identitas?' })
        ).toBeInTheDocument();
        expect(screen.getByRole('button', { name: /Lanjut/ })).toBeInTheDocument();
    });

    it('submits multiple-choice selections as an array', async () => {
        vi.mocked(axios.get).mockResolvedValue({
            data: { status: 'success', data: formWithExtraTypes },
        });
        renderSection();

        await screen.findByText('Form Tipe Lengkap');
        await userEvent.click(screen.getByRole('checkbox', { name: 'A' }));
        await userEvent.click(screen.getByRole('checkbox', { name: 'B' }));
        fireEvent.change(screen.getByLabelText('Tanggal Kejadian'), {
            target: { value: '2026-08-30' },
        });
        await userEvent.click(screen.getByRole('button', { name: /Kirim/ }));

        await waitFor(() => {
            expect(axios.post).toHaveBeenCalledWith('/api/feedback-submissions', {
                answers: [
                    { questionId: 'q1', answer: ['A', 'B'] },
                    { questionId: 'q2', answer: '2026-08-30' },
                ],
            });
        });
    });

    it('normalizes a typed date to the yyyy-MM-dd format before submitting', async () => {
        vi.mocked(axios.get).mockResolvedValue({
            data: { status: 'success', data: formWithExtraTypes },
        });
        renderSection();

        await screen.findByText('Form Tipe Lengkap');
        await userEvent.click(screen.getByRole('checkbox', { name: 'A' }));
        fireEvent.change(screen.getByLabelText('Tanggal Kejadian'), {
            target: { value: '2026-8-3' },
        });
        await userEvent.click(screen.getByRole('button', { name: /Kirim/ }));

        await waitFor(() => {
            expect(axios.post).toHaveBeenCalledWith('/api/feedback-submissions', {
                answers: [
                    { questionId: 'q1', answer: ['A'] },
                    { questionId: 'q2', answer: '2026-08-03' },
                ],
            });
        });
    });
});
