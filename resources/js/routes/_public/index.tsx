import { createFileRoute } from '@tanstack/react-router';

import { ErrorState } from '@/components/ErrorState';
import { HomePage } from '@/features/home/HomePage';
import { HomeSkeleton } from '@/features/home/HomeStates';
import { ensurePostList } from '@/features/post/page-data';
import { ensurePageData } from '@/hooks/usePageData';
import { seoHead } from '@/lib/seo';

export const Route = createFileRoute('/_public/')({
    loader: ({ context }) => {
        return Promise.all([
            ensurePostList(context.queryClient, { q: undefined, page: 1, perPage: 5 }),
            ensurePageData(context.queryClient, '/api/important-links', { per_page: 5 }),
            ensurePageData(context.queryClient, '/api/datasets'),
        ]);
    },
    head: () => seoHead('home'),
    pendingComponent: HomeSkeleton,
    errorComponent: ErrorState,
    component: HomePage,
});
