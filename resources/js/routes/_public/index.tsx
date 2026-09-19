import { createFileRoute } from '@tanstack/react-router';

import { ErrorState } from '@/components/ErrorState';
import { HomePage } from '@/features/home/HomePage';
import { HomeSkeleton } from '@/features/home/HomeStates';
import {
    type DatasetsPayload,
    type HomeData,
    type ImportantLinksPayload,
} from '@/features/home/types';
import { type PostSearchPayload } from '@/features/search/types';
import { ensurePageData, pageQueryKey } from '@/hooks/usePageData';
import { seoHead } from '@/lib/seo';

interface HomeInitialData {
    posts: HomeData['latest_posts'];
    links: HomeData['latest_links'];
    datasets: HomeData['dashboard'];
}

function isHomeInitialData(value: unknown): value is HomeInitialData {
    if (typeof value !== 'object' || value === null) {
        return false;
    }

    const candidate = value as Record<string, unknown>;

    return (
        Array.isArray(candidate.posts) &&
        Array.isArray(candidate.links) &&
        Array.isArray(candidate.datasets)
    );
}

function buildListMeta(total: number, perPage: number) {
    return {
        current_page: 1,
        per_page: perPage,
        total,
        last_page: 1,
    };
}

export const Route = createFileRoute('/_public/')({
    loader: ({ context }) => {
        const initialData = (window as { __INITIAL_DATA__?: unknown }).__INITIAL_DATA__;

        if (isHomeInitialData(initialData)) {
            const postsPayload: PostSearchPayload = {
                status: 'success',
                data: initialData.posts,
                meta: buildListMeta(initialData.posts.length, 5),
            };
            const linksPayload: ImportantLinksPayload = {
                status: 'success',
                data: initialData.links,
                meta: buildListMeta(initialData.links.length, 5),
            };
            const datasetsPayload: DatasetsPayload = {
                status: 'success',
                data: initialData.datasets,
            };

            context.queryClient.setQueryData(
                pageQueryKey('/api/posts', { limit: 5 }),
                postsPayload
            );
            context.queryClient.setQueryData(
                pageQueryKey('/api/important-links', { limit: 5 }),
                linksPayload
            );
            context.queryClient.setQueryData(pageQueryKey('/api/datasets'), datasetsPayload);
            (window as { __INITIAL_DATA__?: unknown }).__INITIAL_DATA__ = null;

            return Promise.resolve([postsPayload, linksPayload, datasetsPayload]);
        }

        return Promise.all([
            ensurePageData(context.queryClient, '/api/posts', { limit: 5 }),
            ensurePageData(context.queryClient, '/api/important-links', { limit: 5 }),
            ensurePageData(context.queryClient, '/api/datasets'),
        ]);
    },
    head: () => seoHead('home'),
    pendingComponent: HomeSkeleton,
    errorComponent: ErrorState,
    component: HomePage,
});
