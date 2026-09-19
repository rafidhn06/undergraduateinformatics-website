import { useSuspensePageData } from '@/hooks/usePageData';
import { type ApiSuccessResponse } from '@/types/api';

import { type LinkSection } from './types';

export function useLinks() {
    return useSuspensePageData<ApiSuccessResponse<LinkSection[]>, LinkSection[]>(
        '/api/link-sections',
        {
            select: (response) => response.data,
        }
    );
}
