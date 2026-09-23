import { type ApiSuccessResponse } from '@/types/api';
import { type PostSummary } from '@/types/post';

import type { PostListMeta } from '../post/page-data';

export interface PostSearchMeta {
    current_page: number;
    per_page: number;
    total: number;
    last_page: number;
}

export interface PostSearchPayload extends ApiSuccessResponse<PostSummary[]> {
    meta: PostSearchMeta;
}

export interface SearchResult {
    posts: PostSummary[];
    meta: PostListMeta;
}
