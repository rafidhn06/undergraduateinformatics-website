import { QueryClient } from '@tanstack/react-query';
import { isNotFound } from '@tanstack/react-router';

import { AxiosError } from 'axios';
import { describe, expect, it, vi } from 'vitest';

import { httpGet } from '@/lib/http';

import { ensureDetailPageData } from './detail-loader';

vi.mock('@/lib/http', () => ({ httpGet: vi.fn() }));

function createQueryClient() {
    return new QueryClient({ defaultOptions: { queries: { retry: false } } });
}

describe('ensureDetailPageData', () => {
    it('resolves the fetched payload and caches it', async () => {
        const queryClient = createQueryClient();
        const payload = { status: 'success', data: { id: 2 } };
        vi.mocked(httpGet).mockResolvedValue(payload);

        await expect(ensureDetailPageData(queryClient, '/api/posts/b')).resolves.toEqual(payload);
        expect(httpGet).toHaveBeenCalledWith('/api/posts/b', undefined);
    });

    it('throws a router notFound on 404 and rethrows anything else', async () => {
        const queryClient = createQueryClient();
        const missing = new AxiosError('missing');
        missing.response = { status: 404 } as never;
        vi.mocked(httpGet).mockRejectedValueOnce(missing);

        const caught = await ensureDetailPageData(queryClient, '/api/posts/c').catch(
            (error) => error
        );
        expect(isNotFound(caught)).toBe(true);

        const broken = new Error('boom');
        vi.mocked(httpGet).mockRejectedValueOnce(broken);

        await expect(ensureDetailPageData(queryClient, '/api/posts/d')).rejects.toBe(broken);
    });
});
