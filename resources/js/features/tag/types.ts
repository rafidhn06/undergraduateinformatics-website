import { type ApiSuccessResponse } from '@/types/api';
import { type PostSummary } from '@/types/post';
import { type Tag } from '@/types/tag';

export interface TagWithCount extends Tag {
    postsCount: number;
}

export interface TagWithPosts extends Tag {
    posts: PostSummary[];
}

export type TagWithCountsPayload = ApiSuccessResponse<TagWithCount[]>;
export type TagWithPostsPayload = ApiSuccessResponse<TagWithPosts>;
