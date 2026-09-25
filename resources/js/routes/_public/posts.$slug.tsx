import { createFileRoute, useParams } from '@tanstack/react-router';

import { ErrorState } from '@/components/ErrorState';
import { NotFoundPage } from '@/components/NotFoundPage';
import { PostPage } from '@/features/post/PostPage';
import { PostSkeleton } from '@/features/post/PostStates';
import { type PostPayload } from '@/features/post/types';
import { ensureDetailPageData } from '@/hooks/usePageData';
import { seoHead, seoTitle } from '@/lib/seo';

export const Route = createFileRoute('/_public/posts/$slug')({
    loader: ({ context, params }) =>
        ensureDetailPageData<PostPayload>(context.queryClient, `/api/posts/${params.slug}`),
    head: ({ loaderData }) => {
        const post = loaderData?.data;

        return seoHead('postDetail', {
            title: post ? seoTitle(post.title) : undefined,
            description: post?.subtitle || undefined,
        });
    },
    pendingComponent: PostSkeleton,
    pendingMs: 0,
    pendingMinMs: 0,
    errorComponent: PostErrorComponent,
    notFoundComponent: NotFoundPage,
    component: PostRouteComponent,
});

function PostRouteComponent() {
    const { slug } = useParams({ from: '/_public/posts/$slug' });
    return <PostPage slug={slug} />;
}

function PostErrorComponent() {
    return <ErrorState />;
}
