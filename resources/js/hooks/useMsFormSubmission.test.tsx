import { type ReactNode } from 'react';

import { QueryClient, QueryClientProvider } from '@tanstack/react-query';

import { act, renderHook, waitFor } from '@testing-library/react';
import { AxiosError, type AxiosResponse } from 'axios';
import { beforeEach, describe, expect, it, vi } from 'vitest';

import { httpPost } from '@/lib/http';

import { type MsFormQuestion, type MsFormValues } from '../types/ms-forms';
import { useMsFormSubmission } from './useMsFormSubmission';

vi.mock('@/lib/http', () => ({
    httpPost: vi.fn(),
}));

const questions: MsFormQuestion[] = [
    {
        id: 'q1',
        title: { text: 'Isi Masukan' },
        subtitle: null,
        type: 'text',
        required: true,
        multiple: false,
        choices: [],
    },
];

const wrapper = ({ children }: { children: ReactNode }) => {
    const queryClient = new QueryClient({
        defaultOptions: {
            mutations: { retry: false },
        },
    });

    return <QueryClientProvider client={queryClient}>{children}</QueryClientProvider>;
};

function axiosError(status: number, data: Record<string, unknown> = {}) {
    const error = new AxiosError('Request failed');
    error.response = { status, data } as AxiosResponse;
    return error;
}

describe('useMsFormSubmission', () => {
    beforeEach(() => {
        vi.mocked(httpPost).mockResolvedValue({ success: true });
    });

    it('submits the non-empty answers to the given url', async () => {
        const { result } = renderHook(() => useMsFormSubmission('/api/feedback', [], questions), {
            wrapper,
        });

        const values: MsFormValues = { q1: 'Masukan saya' };

        await act(async () => {
            result.current.submitForm.mutate(values);
        });

        expect(httpPost).toHaveBeenCalledWith('/api/feedback', {
            answers: [{ questionId: 'q1', answer: 'Masukan saya' }],
        });
    });

    it('shows the generic error message on a non-404 failure', async () => {
        vi.mocked(httpPost).mockRejectedValue(axiosError(422));
        const { result } = renderHook(() => useMsFormSubmission('/api/feedback', [], questions), {
            wrapper,
        });

        await act(async () => {
            result.current.submitForm.mutate({ q1: 'Masukan saya' });
        });

        expect(result.current.submitError).toBe(
            'Gagal mengirim jawaban. Silakan coba beberapa saat lagi.'
        );
    });

    it('shows the unavailable message on a 404 failure', async () => {
        vi.mocked(httpPost).mockRejectedValue(axiosError(404));
        const { result } = renderHook(() => useMsFormSubmission('/api/feedback', [], questions), {
            wrapper,
        });

        await act(async () => {
            result.current.submitForm.mutate({ q1: 'Masukan saya' });
        });

        expect(result.current.submitError).toBe('Formulir sedang tidak tersedia.');
    });

    it('clears the error via resetSubmitError', async () => {
        vi.mocked(httpPost).mockRejectedValue(axiosError(422));
        const { result } = renderHook(() => useMsFormSubmission('/api/feedback', [], questions), {
            wrapper,
        });

        await act(async () => {
            result.current.submitForm.mutate({ q1: 'Masukan saya' });
        });

        expect(result.current.submitError).not.toBeNull();

        await act(async () => {
            result.current.resetSubmitError();
        });

        expect(result.current.submitError).toBeNull();
    });

    it('exposes server field errors from a 422 response', async () => {
        vi.mocked(httpPost).mockRejectedValue(
            axiosError(422, { errors: { date: ['The schedule is already full.'] } })
        );

        const { result } = renderHook(() => useMsFormSubmission('/api/feedback', [], []), {
            wrapper,
        });

        act(() => {
            result.current.submitForm.mutate({});
        });

        await waitFor(() => {
            expect(result.current.fieldErrors).toEqual({
                date: ['The schedule is already full.'],
            });
        });

        expect(result.current.submitError).toBeNull();
    });

    it('shows the generic message when a 422 has no field errors', async () => {
        vi.mocked(httpPost).mockRejectedValue(axiosError(422));

        const { result } = renderHook(() => useMsFormSubmission('/api/feedback', [], []), {
            wrapper,
        });

        act(() => {
            result.current.submitForm.mutate({});
        });

        await waitFor(() => {
            expect(result.current.submitError).toBe(
                'Gagal mengirim jawaban. Silakan coba beberapa saat lagi.'
            );
        });

        expect(result.current.fieldErrors).toBeNull();
    });
});
