import { createFileRoute } from '@tanstack/react-router';

import { ErrorState } from '@/components/ErrorState';
import { LinksPage } from '@/features/links/LinksPage';
import { LinksSkeleton } from '@/features/links/LinksStates';
import { ensurePageData } from '@/hooks/usePageData';
import { seoHead } from '@/lib/seo';

export const Route = createFileRoute('/_public/links')({
    loader: ({ context }) => ensurePageData(context.queryClient, '/api/link-sections'),
    head: () => seoHead('links'),
    pendingComponent: LinksSkeleton,
    errorComponent: ErrorState,
    component: LinksPage,
});
