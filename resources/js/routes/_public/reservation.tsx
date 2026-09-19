import { createFileRoute } from '@tanstack/react-router';

import { MsFormError } from '@/components/MsFormStates';
import { ReservationSkeleton } from '@/features/reservation/ReservationStates';
import { ensurePageData } from '@/hooks/usePageData';
import { seoHead } from '@/lib/seo';

export const Route = createFileRoute('/_public/reservation')({
    loader: ({ context }) => ensurePageData(context.queryClient, '/api/reservation-form'),
    head: () => seoHead('reservation'),
    pendingComponent: ReservationSkeleton,
    errorComponent: MsFormError,
});
