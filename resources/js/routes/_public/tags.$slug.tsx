import { createFileRoute, useParams } from '@tanstack/react-router';

import { ErrorState } from '@/components/ErrorState';
import { NotFoundPage } from '@/components/NotFoundPage';
import { TagDetailPage } from '@/features/tag/TagDetailPage';
import { TagDetailSkeleton } from '@/features/tag/TagDetailStates';
import { type TagWithPostsPayload } from '@/features/tag/types';
import { ensureDetailPageData } from '@/hooks/usePageData';
import { seoHead, seoTitle } from '@/lib/seo';

export const Route = createFileRoute('/_public/tags/$slug')({
    loader: ({ context, params }) =>
        ensureDetailPageData<TagWithPostsPayload>(context.queryClient, `/api/tags/${params.slug}`),
    head: ({ loaderData }) => {
        const tag = loaderData?.data;

        return seoHead('tagDetail', {
            title: tag ? seoTitle(tag.name) : undefined,
            description: tag?.description || undefined,
        });
    },
    pendingComponent: TagDetailSkeleton,
    pendingMs: 0,
    pendingMinMs: 0,
    errorComponent: TagErrorComponent,
    notFoundComponent: NotFoundPage,
    component: TagDetailRouteComponent,
});

function TagDetailRouteComponent() {
    const { slug } = useParams({ from: '/_public/tags/$slug' });
    return <TagDetailPage slug={slug} />;
}

function TagErrorComponent() {
    return <ErrorState />;
}
