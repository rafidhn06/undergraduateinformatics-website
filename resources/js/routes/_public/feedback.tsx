import { createFileRoute } from '@tanstack/react-router';

import { MsFormError } from '@/components/MsFormStates';
import { FeedbackSkeleton } from '@/features/feedback/FeedbackStates';
import { ensurePageData } from '@/hooks/usePageData';
import { seoHead } from '@/lib/seo';

export const Route = createFileRoute('/_public/feedback')({
    loader: ({ context }) => ensurePageData(context.queryClient, '/api/feedback-form'),
    head: () => seoHead('feedback'),
    pendingComponent: FeedbackSkeleton,
    errorComponent: MsFormError,
});
