import { type ReactNode, Suspense } from 'react';

import { QueryClient, QueryClientProvider } from '@tanstack/react-query';

import { renderHook, waitFor } from '@testing-library/react';
import axios from 'axios';
import { beforeEach, describe, expect, it, vi } from 'vitest';

import { useMsForm } from './useMsForm';

vi.mock('axios', async () => {
    const actual = await vi.importActual<typeof import('axios')>('axios');

    return {
        ...actual,
        default: {
            ...actual.default,
            get: vi.fn(),
        },
    };
});

const wrapper = ({ children }: { children: ReactNode }) => {
    const queryClient = new QueryClient({
        defaultOptions: {
            queries: { retry: false },
        },
    });

    return (
        <QueryClientProvider client={queryClient}>
            <Suspense fallback={null}>{children}</Suspense>
        </QueryClientProvider>
    );
};

describe('useMsForm', () => {
    beforeEach(() => {
        delete window.__INITIAL_DATA__;
    });

    it('unwraps the api envelope and marks a complete form as valid', async () => {
        vi.mocked(axios.get).mockResolvedValue({
            data: {
                status: 'success',
                data: {
                    link: 'https://forms.office.com/r/abc123',
                    questions: [{ id: 'q1', title: 'Q' }],
                },
            },
        });

        const { result } = renderHook(() => useMsForm('/api/feedback'), { wrapper });

        await waitFor(() => {
            expect(result.current.data).toBeDefined();
        });

        expect(result.current.data?.isValid).toBe(true);
        expect(result.current.data?.link).toBe('https://forms.office.com/r/abc123');
    });

    it('marks a form without questions as invalid', async () => {
        vi.mocked(axios.get).mockResolvedValue({
            data: {
                status: 'success',
                data: { link: 'https://forms.office.com/r/abc123', questions: [] },
            },
        });

        const { result } = renderHook(() => useMsForm('/api/feedback'), { wrapper });

        await waitFor(() => {
            expect(result.current.data).toBeDefined();
        });

        expect(result.current.data?.isValid).toBe(false);
    });

    it('marks a form without a link as invalid', async () => {
        vi.mocked(axios.get).mockResolvedValue({
            data: {
                status: 'success',
                data: { link: null, questions: [{ id: 'q1', title: 'Q' }] },
            },
        });

        const { result } = renderHook(() => useMsForm('/api/feedback'), { wrapper });

        await waitFor(() => {
            expect(result.current.data).toBeDefined();
        });

        expect(result.current.data?.isValid).toBe(false);
    });
});
