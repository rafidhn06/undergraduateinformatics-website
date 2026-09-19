import type { ReactNode } from 'react';

import { QueryClient, QueryClientProvider } from '@tanstack/react-query';

import { renderHook, waitFor } from '@testing-library/react';
import axios from 'axios';
import { beforeEach, describe, expect, it, vi } from 'vitest';

import { usePostSearch } from './usePostSearch';

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

function wrapper({ children }: { children: ReactNode }) {
    const queryClient = new QueryClient({
        defaultOptions: {
            queries: { retry: false },
        },
    });

    return <QueryClientProvider client={queryClient}>{children}</QueryClientProvider>;
}

describe('usePostSearch', () => {
    beforeEach(() => {
        vi.mocked(axios.get).mockReset();
        vi.mocked(axios.get).mockResolvedValue({
            data: {
                status: 'success',
                data: [],
                meta: { current_page: 1, per_page: 10, total: 0, last_page: 1 },
            },
        });
        delete (window as { __INITIAL_DATA__?: unknown }).__INITIAL_DATA__;
    });

    it('requests /api/posts with q page per_page', async () => {
        renderHook(() => usePostSearch('rilis', 1, 10), { wrapper });

        await waitFor(() => expect(axios.get).toHaveBeenCalled());

        expect(axios.get).toHaveBeenCalledWith('/api/posts', {
            params: { q: 'rilis', page: 1, per_page: 10 },
        });
        const params = vi.mocked(axios.get).mock.calls[0]?.[1]?.params as Record<string, unknown>;
        expect(params).not.toHaveProperty('limit');
    });
});
