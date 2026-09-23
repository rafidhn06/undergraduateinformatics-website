import { QueryClient, QueryClientProvider } from '@tanstack/react-query';

import { render, screen, waitFor } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import axios, { AxiosError, type AxiosResponse } from 'axios';
import { beforeEach, describe, expect, it, vi } from 'vitest';

import { MsForm } from './MsForm';
import {
    SUBMIT_URL,
    branchingPayload,
    optionalPayload,
    richPayload,
    richTextPayload,
    simplePayload,
    toFormProps,
} from './ms-form-fixtures';

vi.mock('axios', async () => {
    const actual = await vi.importActual<typeof import('axios')>('axios');

    return {
        ...actual,
        default: {
            ...actual.default,
            post: vi.fn(),
        },
    };
});

function axiosError(status: number) {
    const error = new AxiosError('Request failed');
    error.response = { status, data: {} } as AxiosResponse;
    return error;
}

function renderForm(props: Parameters<typeof MsForm>[0]) {
    const queryClient = new QueryClient({
        defaultOptions: {
            queries: { retry: false },
        },
    });

    return render(
        <QueryClientProvider client={queryClient}>
            <MsForm {...props} />
        </QueryClientProvider>
    );
}

describe('MsForm', () => {
    beforeEach(() => {
        vi.mocked(axios.post).mockResolvedValue({ data: { success: true } });
    });

    it('renders title, description, and the first section questions', () => {
        renderForm(toFormProps(richPayload));

        expect(screen.getByRole('heading', { name: 'Form Umpan Balik' })).toBeInTheDocument();
        expect(
            screen.getByText(
                'Bantu kami meningkatkan layanan dengan mengisi formulir di bawah ini.'
            )
        ).toBeInTheDocument();
        expect(screen.getByRole('heading', { name: 'Jenis Masukan' })).toBeInTheDocument();
    });

    it('renders rich form title, description, section, and question titles', () => {
        renderForm(toFormProps(richTextPayload));

        expect(screen.getByRole('heading', { level: 1 })).toHaveTextContent('Form Umpan Balik');
        expect(screen.getByRole('heading', { level: 1 })).toContainHTML('<b>Umpan</b>');
        expect(
            screen.getByText(
                (_, element) => element?.textContent === 'Bantu kami meningkatkan layanan.'
            )
        ).toBeInTheDocument();
        expect(screen.getByRole('heading', { level: 2 })).toHaveTextContent('Jenis Masukan');
        expect(
            screen.getByText((_, element) => element?.textContent === 'Pilih satu atau lebih.')
        ).toBeInTheDocument();
        expect(screen.getByRole('heading', { level: 3 })).toHaveTextContent('Aspek yang Dinilai');
        const questionSubtitle = screen.getByText(
            (_, element) => element?.textContent === 'Tulis rincian Anda.'
        );
        expect(questionSubtitle).toBeInTheDocument();
    });

    it('renders section title and subtitle', () => {
        renderForm(toFormProps(branchingPayload));

        expect(
            screen.getByRole('heading', { level: 2, name: 'Jenis Masukan' })
        ).toBeInTheDocument();
        expect(
            screen.getByText('Jawaban Anda menentukan bagian yang akan diisi.')
        ).toBeInTheDocument();
    });

    it('marks required questions with an asterisk', () => {
        renderForm(toFormProps(simplePayload));

        expect(screen.getByText('*')).toBeInTheDocument();
    });

    it('advances to the next section and offers a back button', async () => {
        renderForm(toFormProps(branchingPayload));

        await userEvent.click(screen.getByRole('radio', { name: 'Saran' }));
        await userEvent.click(screen.getByRole('button', { name: /Lanjut/ }));

        expect(await screen.findByRole('heading', { name: 'Detail Saran' })).toBeInTheDocument();
        expect(screen.getByRole('button', { name: /Kembali/ })).toBeInTheDocument();
    });

    it('returns to the previous section when going back', async () => {
        renderForm(toFormProps(branchingPayload));

        await userEvent.click(screen.getByRole('radio', { name: 'Saran' }));
        await userEvent.click(screen.getByRole('button', { name: /Lanjut/ }));

        await screen.findByRole('heading', { level: 2, name: 'Detail Saran' });
        await userEvent.click(screen.getByRole('button', { name: /Kembali/ }));

        expect(
            screen.getByRole('heading', { level: 2, name: 'Jenis Masukan' })
        ).toBeInTheDocument();
        expect(screen.queryByRole('button', { name: /Kembali/ })).not.toBeInTheDocument();
    });

    it('requires answers to visible questions before advancing', async () => {
        renderForm(toFormProps(branchingPayload));

        await userEvent.click(screen.getByRole('button', { name: /Lanjut/ }));

        expect(await screen.findAllByText(/wajib diisi/)).toHaveLength(1);
        expect(axios.post).not.toHaveBeenCalled();
    });

    it('scrolls to the first invalid question when Lanjut fails', async () => {
        const scrollIntoView = vi.mocked(Element.prototype.scrollIntoView);
        scrollIntoView.mockClear();

        renderForm(toFormProps(branchingPayload));
        await userEvent.click(screen.getByRole('button', { name: /Lanjut/ }));

        await screen.findAllByText(/wajib diisi/);
        expect(scrollIntoView).toHaveBeenCalledWith({ behavior: 'smooth', block: 'center' });
        expect(scrollIntoView.mock.instances[0]).toBe(
            document.querySelector('[data-question-id="jenis"]')
        );
    });

    it('shows the submit button on the final section', async () => {
        renderForm(toFormProps(branchingPayload));

        await userEvent.click(screen.getByRole('radio', { name: 'Saran' }));
        await userEvent.click(screen.getByRole('button', { name: /Lanjut/ }));

        await screen.findByRole('heading', { level: 2, name: 'Detail Saran' });
        await userEvent.type(screen.getByRole('textbox', { name: 'Tuliskan Saran Anda' }), 'Saran');
        await userEvent.click(screen.getByRole('radio', { name: 'Besar' }));

        expect(await screen.findByRole('button', { name: /Kirim/ })).toBeInTheDocument();
        expect(screen.queryByRole('button', { name: /Lanjut/ })).not.toBeInTheDocument();
    });

    it('blocks submit until all reachable required questions are answered', async () => {
        renderForm(toFormProps(richPayload));

        await userEvent.click(screen.getByRole('radio', { name: 'Saran' }));
        await userEvent.click(screen.getByRole('button', { name: /Kirim/ }));

        expect(await screen.findAllByText('Pertanyaan ini wajib diisi')).toHaveLength(2);
        expect(axios.post).not.toHaveBeenCalled();
    });

    it('shows required-field errors when submitting an empty required form', async () => {
        renderForm(toFormProps(simplePayload));

        expect(screen.getByRole('button', { name: /Kirim/ })).toBeEnabled();
        await userEvent.click(screen.getByRole('button', { name: /Kirim/ }));

        expect(await screen.findByText('Pertanyaan ini wajib diisi')).toBeInTheDocument();
        expect(axios.post).not.toHaveBeenCalled();
    });

    it('shows text-only errors without red borders on invalid fields', async () => {
        renderForm(toFormProps(richPayload));

        await userEvent.click(screen.getByRole('radio', { name: 'Saran' }));
        await userEvent.type(screen.getByLabelText('Tanggal Pengalaman'), 'bukan-tanggal');
        await userEvent.click(screen.getByRole('button', { name: /Kirim/ }));

        const messages = await screen.findAllByText('Pertanyaan ini wajib diisi');
        expect(messages).toHaveLength(2);
        messages.forEach((message) => expect(message).toHaveClass('text-destructive'));

        const textbox = screen.getByRole('textbox', { name: 'Isi Masukan' });
        expect(textbox).toHaveAttribute('aria-invalid', 'true');
        expect(textbox.getAttribute('class')).not.toContain('destructive');

        const checkbox = screen.getByRole('checkbox', { name: 'Akademik' });
        expect(checkbox).toHaveAttribute('aria-invalid', 'true');
        expect(checkbox.getAttribute('class')).not.toContain('destructive');

        const dateGroup = document.querySelector('[data-slot="input-group"]');
        expect(dateGroup?.getAttribute('class')).not.toContain('destructive');

        expect(document.querySelector('[data-invalid="true"]')).not.toBeInTheDocument();
    });

    it('shows text-only errors without red borders on invalid radio fields', async () => {
        renderForm(toFormProps(branchingPayload));

        await userEvent.click(screen.getByRole('button', { name: /Lanjut/ }));

        await screen.findByText(/wajib diisi/);

        const radio = screen.getByRole('radio', { name: 'Saran' });
        expect(radio).toHaveAttribute('aria-invalid', 'true');
        expect(radio.getAttribute('class')).not.toContain('destructive');
        expect(document.querySelector('[data-invalid="true"]')).not.toBeInTheDocument();
    });

    it('rejects submitting a fully empty form with a message', async () => {
        renderForm(toFormProps(optionalPayload));

        await userEvent.click(screen.getByRole('button', { name: /Kirim/ }));

        expect(
            await screen.findByText('Isi minimal satu jawaban terlebih dahulu.')
        ).toBeInTheDocument();
        expect(axios.post).not.toHaveBeenCalled();

        await userEvent.type(screen.getByRole('textbox'), 'Pesan saya');

        expect(
            screen.queryByText('Isi minimal satu jawaban terlebih dahulu.')
        ).not.toBeInTheDocument();
        await userEvent.click(screen.getByRole('button', { name: /Kirim/ }));

        await waitFor(() => {
            expect(axios.post).toHaveBeenCalled();
        });
    });

    it('submits answers and shows the success state', async () => {
        renderForm(toFormProps(simplePayload));

        await userEvent.type(screen.getByRole('textbox'), 'Masukan saya');
        await userEvent.click(screen.getByRole('button', { name: /Kirim/ }));

        await waitFor(() => {
            expect(axios.post).toHaveBeenCalledWith(SUBMIT_URL, {
                answers: [{ questionId: 'isi', answer: 'Masukan saya' }],
            });
        });

        expect(await screen.findByText('Terima kasih!')).toBeInTheDocument();
    });

    it('does not scroll to a question when validation passes', async () => {
        const scrollIntoView = vi.mocked(Element.prototype.scrollIntoView);
        scrollIntoView.mockClear();

        renderForm(toFormProps(simplePayload));
        await userEvent.type(screen.getByRole('textbox'), 'Masukan saya');
        await userEvent.click(screen.getByRole('button', { name: /Kirim/ }));

        await screen.findByText('Terima kasih!');
        expect(scrollIntoView).not.toHaveBeenCalled();
    });

    it('resets the form after filling it again', async () => {
        renderForm(toFormProps(simplePayload));

        await userEvent.type(screen.getByRole('textbox'), 'Masukan saya');
        await userEvent.click(screen.getByRole('button', { name: /Kirim/ }));

        await screen.findByText('Terima kasih!');
        await userEvent.click(screen.getByRole('button', { name: /Isi Formulir Lagi/ }));

        expect(await screen.findByRole('textbox')).toHaveValue('');
        expect(screen.getByRole('button', { name: /Kirim/ })).toBeInTheDocument();
    });

    it('shows an error message when the submit request fails', async () => {
        vi.mocked(axios.post).mockRejectedValue(axiosError(422));
        renderForm(toFormProps(simplePayload));

        await userEvent.type(screen.getByRole('textbox'), 'Masukan saya');
        await userEvent.click(screen.getByRole('button', { name: /Kirim/ }));

        expect(
            await screen.findByText('Gagal mengirim jawaban. Silakan coba beberapa saat lagi.')
        ).toBeInTheDocument();
    });

    it('shows an unavailable message when the submit returns 404', async () => {
        vi.mocked(axios.post).mockRejectedValue(axiosError(404));
        renderForm(toFormProps(simplePayload));

        await userEvent.type(screen.getByRole('textbox'), 'Masukan saya');
        await userEvent.click(screen.getByRole('button', { name: /Kirim/ }));

        expect(await screen.findByText('Formulir sedang tidak tersedia.')).toBeInTheDocument();
    });
});
