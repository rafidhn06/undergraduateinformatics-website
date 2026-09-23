import { QueryClient } from '@tanstack/react-query';

import { beforeEach, describe, expect, it, vi } from 'vitest';

import { httpGet } from '@/lib/http';

import {
    ensurePageData,
    fetchPageData,
    isSeedEnvelope,
    normalizeQueryParams,
    pageQueryKey,
    seedInitialQueries,
} from './usePageData';

vi.mock('@/lib/http', () => ({
    httpGet: vi.fn(),
}));

describe('ensurePageData', () => {
    let queryClient: QueryClient;

    beforeEach(() => {
        queryClient = new QueryClient({
            defaultOptions: {
                queries: {
                    retry: false,
                },
            },
        });
        vi.clearAllMocks();
        delete window.__INITIAL_DATA__;
    });

    it('uses seeded cache without fetching', async () => {
        const initialPayload = { status: 'success', data: [{ id: 1 }] };
        queryClient.setQueryData(pageQueryKey('/api/posts', { per_page: 5 }), initialPayload);

        const result = await ensurePageData(queryClient, '/api/posts', { per_page: 5 });

        expect(result).toEqual(initialPayload);
        expect(httpGet).not.toHaveBeenCalled();
    });

    it('fetches with shared fetchPageData when cache is empty', async () => {
        const fetchedPayload = { status: 'success', data: [{ id: 2 }] };
        vi.mocked(httpGet).mockResolvedValueOnce(fetchedPayload);

        const result = await ensurePageData<{ status: string }>(queryClient, '/api/posts', {
            per_page: 5,
        });

        expect(result).toEqual(fetchedPayload);
        expect(httpGet).toHaveBeenCalledWith('/api/posts', { per_page: 5 });
    });

    it('ignores stale window data and fetches fresh data', async () => {
        window.__INITIAL_DATA__ = {
            seeds: [
                {
                    endpoint: '/api/posts',
                    params: { per_page: 5 },
                    payload: { status: 'success', data: [{ id: 9 }] },
                },
            ],
        };
        const fetchedPayload = { status: 'success', data: [{ id: 1 }] };
        vi.mocked(httpGet).mockResolvedValueOnce(fetchedPayload);

        const result = await ensurePageData(queryClient, '/api/posts', { per_page: 5 });

        expect(result).toEqual(fetchedPayload);
        expect(httpGet).toHaveBeenCalledWith('/api/posts', { per_page: 5 });
        expect(queryClient.getQueryData(pageQueryKey('/api/posts', { per_page: 5 }))).toEqual(
            fetchedPayload
        );
    });

    it('fetchPageData resolves response data with params', async () => {
        const fetchedPayload = { status: 'success' };
        vi.mocked(httpGet).mockResolvedValueOnce(fetchedPayload);

        await expect(fetchPageData('/api/tags', { page: 2 })).resolves.toEqual(fetchedPayload);
        expect(httpGet).toHaveBeenCalledWith('/api/tags', { page: 2 });
    });

    it('seeds query cache once and clears global', async () => {
        const payload = { status: 'success', data: [{ id: 1 }] };
        window.__INITIAL_DATA__ = {
            seeds: [{ endpoint: '/api/posts', params: { per_page: 5 }, payload }],
        };

        seedInitialQueries(queryClient);

        expect(queryClient.getQueryData(pageQueryKey('/api/posts', { per_page: 5 }))).toEqual(
            payload
        );
        expect(window.__INITIAL_DATA__).toBeNull();
    });

    it('treats empty array params from php as param-less key', () => {
        expect(pageQueryKey('/api/datasets', [] as unknown as undefined)).toEqual(
            pageQueryKey('/api/datasets')
        );
        expect(normalizeQueryParams([])).toBeUndefined();
    });

    it('detects seed envelope only for seeds arrays', () => {
        expect(isSeedEnvelope({ seeds: [] })).toBe(true);
        expect(isSeedEnvelope({ notFound: true })).toBe(false);
        expect(isSeedEnvelope(null)).toBe(false);
    });
});
