import { createFileRoute } from '@tanstack/react-router';

import { ErrorState } from '@/components/ErrorState';
import { ensurePostList, normalizePostSearch } from '@/features/post/page-data';
import { SearchPage } from '@/features/search/SearchPage';
import { SearchSkeleton } from '@/features/search/SearchStates';
import { seoHead } from '@/lib/seo';

const searchValidator = {
    parse(input: Record<string, unknown>) {
        return normalizePostSearch(input);
    },
};

export const Route = createFileRoute('/_public/posts/')({
    validateSearch: searchValidator,
    loader: ({ context, location }) => {
        const search = normalizePostSearch(location.search as Record<string, unknown>);

        return ensurePostList(context.queryClient, search);
    },
    head: () => seoHead('postSearch'),
    pendingComponent: SearchSkeleton,
    errorComponent: ErrorState,
    component: SearchRouteComponent,
});

function SearchRouteComponent() {
    const { q, page, perPage } = Route.useSearch();

    return <SearchPage q={q ?? ''} page={page} perPage={perPage} />;
}
