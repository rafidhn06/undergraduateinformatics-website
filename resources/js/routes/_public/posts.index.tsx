import { createFileRoute } from '@tanstack/react-router';

import { ErrorState } from '@/components/ErrorState';
import { SearchPage } from '@/features/search/SearchPage';
import { SearchSkeleton } from '@/features/search/SearchStates';
import { ensurePageData } from '@/hooks/usePageData';
import { seoHead } from '@/lib/seo';

interface PostsSearch {
    q?: string;
    page?: number;
    per_page?: number;
}

function parsePositiveInt(raw: unknown): number | undefined {
    if (raw === undefined || raw === null || raw === '') {
        return undefined;
    }

    const value = typeof raw === 'number' ? raw : Number(raw);

    return Number.isInteger(value) && value >= 1 ? value : undefined;
}

const searchValidator = {
    parse(input: Record<string, unknown>): PostsSearch {
        const q = typeof input.q === 'string' ? input.q : undefined;
        const page = parsePositiveInt(input.page) ?? (input.page === undefined ? undefined : 1);
        const per_page = parsePositiveInt(input.per_page) ?? (input.per_page === undefined ? undefined : 10);

        return { q, page, per_page };
    },
};

export const Route = createFileRoute('/_public/posts/')({
    validateSearch: searchValidator,
    loader: ({ context, location }) => {
        const search = location.search as PostsSearch;

        return ensurePageData(context.queryClient, '/api/posts', {
            q: search.q ?? undefined,
            page: search.page ?? 1,
            per_page: search.per_page ?? 10,
        });
    },
    head: () => seoHead('postSearch'),
    pendingComponent: SearchSkeleton,
    errorComponent: ErrorState,
    component: SearchRouteComponent,
});

function SearchRouteComponent() {
    const { q, page, per_page } = Route.useSearch();

    return <SearchPage q={q ?? ''} page={page ?? 1} perPage={per_page ?? 10} />;
}
