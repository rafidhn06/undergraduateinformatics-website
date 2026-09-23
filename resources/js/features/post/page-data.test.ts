import { describe, expect, it } from 'vitest';

import { POST_LIST_DEFAULTS, normalizePostSearch, postQueryKey } from './page-data';

describe('post page-data seam', () => {
    it('applies defaults page 1 perPage 10', () => {
        expect(normalizePostSearch({})).toEqual({ q: undefined, page: 1, perPage: 10 });
    });

    it('normalizes invalid page and per_page to defaults', () => {
        expect(normalizePostSearch({ page: '0', per_page: 'abc' })).toEqual({
            q: undefined,
            page: 1,
            perPage: 10,
        });
    });

    it('keeps explicit home override perPage 5 in query key', () => {
        expect(postQueryKey({ q: undefined, page: 1, perPage: 5 })).toEqual([
            '/api/posts',
            { q: undefined, page: 1, per_page: 5 },
        ]);
    });

    it('exposes list defaults', () => {
        expect(POST_LIST_DEFAULTS).toEqual({ page: 1, perPage: 10 });
    });
});
