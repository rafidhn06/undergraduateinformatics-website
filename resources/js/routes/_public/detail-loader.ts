import type { QueryClient } from '@tanstack/react-query';
import { notFound } from '@tanstack/react-router';

import { ensurePageData } from '@/hooks/usePageData';
import { isNotFoundError } from '@/lib/errors';

export function ensureDetailPageData<TData = unknown>(
    queryClient: QueryClient,
    endpoint: string
): Promise<TData> {
    return ensurePageData<TData>(queryClient, endpoint).catch((error: unknown) => {
        if (isNotFoundError(error)) {
            throw notFound();
        }
        throw error;
    });
}
