import { QueryClient } from '@tanstack/react-query';

import axios from 'axios';
import { beforeEach, describe, expect, it, vi } from 'vitest';

import { type NormalizedPostSearch } from '@/features/post/page-data';
import { pageQueryKey } from '@/hooks/usePageData';
import { seoPage } from '@/lib/seo';

import { Route } from './posts.index';

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

function evaluateSearch(input: unknown): NormalizedPostSearch {
    const validator = Route.options.validateSearch as {
        ['~standard']?: { validate: (value: unknown) => { value: NormalizedPostSearch } };
        parse?: (value: unknown) => NormalizedPostSearch;
    };

    if (validator && '~standard' in validator) {
        return validator['~standard']!.validate(input).value;
    }
    if (validator && typeof validator.parse === 'function') {
        return validator.parse(input);
    }
    return { q: undefined, page: 1, perPage: 10 };
}

const loader = Route.options.loader as unknown as (args: {
    context: { queryClient: QueryClient };
    location: { search: Record<string, unknown> };
}) => Promise<unknown>;

function createQueryClient() {
    return new QueryClient({
        defaultOptions: {
            queries: { retry: false },
        },
    });
}

beforeEach(() => {
    vi.mocked(axios.get).mockReset();
    delete (window as { __INITIAL_DATA__?: unknown }).__INITIAL_DATA__;
});

describe('posts index route', () => {
    it('sets the page title and description via the head option', () => {
        const head = Route.options.head as unknown as (context: unknown) => {
            meta?: { title?: string; name?: string; content?: string }[];
        };

        const result = head({});
        expect(result.meta?.[0]?.title).toBe(seoPage('postSearch').title);
        const description = result.meta?.find((entry) => entry.name === 'description');
        expect(description?.content).toBe(seoPage('postSearch').description);
    });

    it('normalizes invalid page values to 1', () => {
        expect(evaluateSearch({ page: '0' })?.page).toBe(1);
        expect(evaluateSearch({ page: 'abc' })?.page).toBe(1);
    });

    it('normalizes invalid per_page values to perPage 10', () => {
        expect(evaluateSearch({ per_page: '0' })?.perPage).toBe(10);
        expect(evaluateSearch({ per_page: 'abc' })?.perPage).toBe(10);
    });

    it('applies page 1 and perPage 10 defaults when not provided', () => {
        expect(evaluateSearch({ q: 'beasiswa' })?.q).toBe('beasiswa');
        expect(evaluateSearch({})).toEqual({ q: undefined, page: 1, perPage: 10 });
    });
});

describe('posts index route loader', () => {
    it('prefetches /api/posts with q page per_page and caches it', async () => {
        const queryClient = createQueryClient();
        const payload = { status: 'success', data: [], meta: {} };
        vi.mocked(axios.get).mockResolvedValue({ data: payload });

        const result = await loader({
            context: { queryClient },
            location: { search: { q: 'beasiswa', page: 2, per_page: 10 } },
        });

        expect(result).toEqual(payload);
        expect(
            queryClient.getQueryData(
                pageQueryKey('/api/posts', { q: 'beasiswa', page: 2, per_page: 10 })
            )
        ).toEqual(payload);
        expect(axios.get).toHaveBeenCalledWith('/api/posts', {
            params: { q: 'beasiswa', page: 2, per_page: 10 },
        });
    });

    it('defaults to page 1 and per_page 10 when the search is empty', async () => {
        const queryClient = createQueryClient();
        vi.mocked(axios.get).mockResolvedValue({ data: { status: 'success' } });

        await loader({ context: { queryClient }, location: { search: {} } });

        expect(axios.get).toHaveBeenCalledWith('/api/posts', {
            params: { q: undefined, page: 1, per_page: 10 },
        });
    });
});
