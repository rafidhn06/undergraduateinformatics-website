import type { QueryClient } from '@tanstack/react-query';

import { ensurePageData, pageQueryKey, useSuspensePageData } from '@/hooks/usePageData';
import type { Post } from '@/types/post';
import type { PostSummary } from '@/types/post';

import type { PostPayload } from './types';

export const POST_LIST_DEFAULTS = { page: 1, perPage: 10 } as const;

export interface PostSearchInput {
    q?: unknown;
    page?: unknown;
    per_page?: unknown;
    perPage?: unknown;
}

export interface NormalizedPostSearch {
    q: string | undefined;
    page: number;
    perPage: number;
}

export interface PostListMeta {
    currentPage: number;
    perPage: number;
    total: number;
    lastPage: number;
}

export interface PostListResult {
    posts: PostSummary[];
    meta: PostListMeta;
}

interface PostListWirePayload {
    data: PostSummary[];
    meta: { current_page: number; per_page: number; total: number; last_page: number };
}

function toPositiveInt(raw: unknown): number | undefined {
    if (raw === undefined || raw === null || raw === '') {
        return undefined;
    }

    const value = typeof raw === 'number' ? raw : Number(raw);

    return Number.isInteger(value) && value >= 1 ? value : undefined;
}

export function normalizePostSearch(input: PostSearchInput): NormalizedPostSearch {
    const rawQuery = (input as { q?: unknown }).q;
    const query = typeof rawQuery === 'string' ? rawQuery : undefined;
    const page = toPositiveInt(input.page) ?? POST_LIST_DEFAULTS.page;
    const perPage = toPositiveInt(input.per_page ?? input.perPage) ?? POST_LIST_DEFAULTS.perPage;

    return { q: query || undefined, page, perPage };
}

function toWireParams(search: { q?: string; page: number; perPage: number }) {
    return { q: search.q, page: search.page, per_page: search.perPage };
}

export function postQueryKey(search: { q?: string; page: number; perPage: number }) {
    return pageQueryKey('/api/posts', toWireParams(search));
}

export function ensurePostList(
    queryClient: QueryClient,
    search: { q?: string; page: number; perPage: number }
) {
    return ensurePageData(queryClient, '/api/posts', toWireParams(search));
}

function mapPostList(response: PostListWirePayload): PostListResult {
    return {
        posts: response.data,
        meta: {
            currentPage: response.meta.current_page,
            perPage: response.meta.per_page,
            total: response.meta.total,
            lastPage: response.meta.last_page,
        },
    };
}

export function usePostList(q: string, page: number, perPage: number = POST_LIST_DEFAULTS.perPage) {
    return useSuspensePageData<PostListWirePayload, PostListResult>(
        '/api/posts',
        { select: mapPostList },
        { q: q || undefined, page, per_page: perPage }
    );
}

export function usePostDetail(slug: string) {
    return useSuspensePageData<PostPayload, Post>(`/api/posts/${slug}`, {
        select: (response) => response.data,
    });
}
