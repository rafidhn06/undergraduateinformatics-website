import { usePageData } from '@/hooks/usePageData';

import { type PostSearchPayload, type SearchResult } from './types';

export function usePostSearch(q: string, page: number, limit = 10) {
    return usePageData<PostSearchPayload, SearchResult>(
        '/api/posts',
        { select: (response) => ({ posts: response.data, meta: response.meta }) },
        { q: q || undefined, page, limit }
    );
}
