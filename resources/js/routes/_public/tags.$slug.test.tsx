import { QueryClient } from '@tanstack/react-query';

import axios from 'axios';
import { beforeEach, describe, expect, it, vi } from 'vitest';

import { pageQueryKey } from '@/hooks/usePageData';
import { seoDefaults, seoPage } from '@/lib/seo';
import { axiosError } from '@/test/mocks';

import { Route } from './tags.$slug';

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

const loader = Route.options.loader as unknown as (args: {
    context: { queryClient: QueryClient };
    params: { slug: string };
}) => Promise<unknown>;

function createQueryClient() {
    return new QueryClient({
        defaultOptions: {
            queries: { retry: false },
        },
    });
}

const payload = { status: 'success', data: { name: 'Beasiswa' } };

beforeEach(() => {
    vi.mocked(axios.get).mockReset();
    delete (window as { __INITIAL_DATA__?: unknown }).__INITIAL_DATA__;
});

describe('tags detail route', () => {
    it('uses the tag name and description from loader data for the head', () => {
        const head = Route.options.head as unknown as (context: { loaderData?: unknown }) => {
            meta?: { title?: string; name?: string; content?: string }[];
        };

        const result = head({
            loaderData: { data: { name: 'Kurikulum', description: 'Info kurikulum' } },
        });
        expect(result.meta?.[0]?.title).toBe(`Kurikulum - ${seoDefaults.title}`);
        const description = result.meta?.find((entry) => entry.name === 'description');
        expect(description?.content).toBe('Info kurikulum');
    });

    it('falls back to the static tagDetail config without loader data', () => {
        const head = Route.options.head as unknown as (context: { loaderData?: unknown }) => {
            meta?: { title?: string; name?: string; content?: string }[];
        };

        const result = head({});
        expect(result.meta?.[0]?.title).toBe(seoPage('tagDetail').title);
        expect(result.meta?.find((entry) => entry.name === 'description')?.content).toBe(
            seoPage('tagDetail').description
        );
    });
});

describe('tags detail route loader', () => {
    it('uses seeded cache without fetching', async () => {
        const queryClient = createQueryClient();
        queryClient.setQueryData(pageQueryKey('/api/tags/beasiswa'), payload);

        const result = await loader({ context: { queryClient }, params: { slug: 'beasiswa' } });

        expect(result).toEqual(payload);
        expect(axios.get).not.toHaveBeenCalled();
    });

    it('fetches and caches on client navigation using the shared query key', async () => {
        const queryClient = createQueryClient();
        vi.mocked(axios.get).mockResolvedValue({ data: payload });

        const result = await loader({ context: { queryClient }, params: { slug: 'kurikulum' } });

        expect(result).toEqual(payload);
        expect(queryClient.getQueryData(pageQueryKey('/api/tags/kurikulum'))).toEqual(payload);
        expect(axios.get).toHaveBeenCalledWith('/api/tags/kurikulum', { params: undefined });
    });

    it('throws notFound when the api returns 404', async () => {
        const queryClient = createQueryClient();
        vi.mocked(axios.get).mockRejectedValue(axiosError(404));

        await expect(loader({ context: { queryClient }, params: { slug: 'x' } })).rejects.toThrow();
    });
});
