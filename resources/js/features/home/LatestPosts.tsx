import { Link } from '@/components/Link';
import { PostCard } from '@/components/PostCard';
import { type PostSummary } from '@/types/post';

interface LatestPostsProps {
    posts: PostSummary[];
}

export function LatestPosts({ posts }: LatestPostsProps) {
    return (
        <section aria-labelledby="latest-posts-heading" className="flex flex-col">
            <h2 id="latest-posts-heading">
                <Link
                    variant="fade"
                    to="/posts"
                    search={{ q: undefined, page: 1, perPage: 10 }}
                    className="font-heading text-foreground hover:text-muted-foreground text-[22.5px] leading-[1.4] font-semibold md:text-[20px]"
                >
                    Informasi Terbaru
                </Link>
            </h2>
            {posts.length === 0 ? (
                <p role="status" className="text-muted-foreground mt-[22.5px] md:mt-5">
                    Belum ada berita atau pengumuman.
                </p>
            ) : (
                <ul className="mt-[22.5px] flex flex-col gap-4 md:mt-5">
                    {posts.map((post) => (
                        <li key={post.id}>
                            <PostCard post={post} />
                        </li>
                    ))}
                </ul>
            )}
        </section>
    );
}
