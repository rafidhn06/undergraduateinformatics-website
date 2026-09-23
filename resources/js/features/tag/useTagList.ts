import { useSuspensePageData } from '@/hooks/usePageData';
import { type ApiSuccessResponse } from '@/types/api';
import { type Tag } from '@/types/tag';

import { type TagWithCount } from './types';

interface TagWithCountWire extends Tag {
    posts_count: number;
}

export function useTagList() {
    return useSuspensePageData<ApiSuccessResponse<TagWithCountWire[]>, TagWithCount[]>(
        '/api/tags',
        {
            select: (response) =>
                response.data.map(({ posts_count, ...rest }) => ({
                    ...rest,
                    postsCount: posts_count,
                })),
        }
    );
}
