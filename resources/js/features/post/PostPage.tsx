import { PostContent } from './PostContent';
import { usePostDetail } from './page-data';

export function PostPage({ slug }: { slug: string }) {
    const { data: post } = usePostDetail(slug);

    return <PostContent post={post} />;
}
