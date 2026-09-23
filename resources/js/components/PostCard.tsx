import { format } from 'date-fns';
import { id } from 'date-fns/locale';

import { Link } from '@/components/Link';

import { type PostSummary } from '../types/post';

interface PostCardProps {
    post: PostSummary;
}

export function PostCard({ post }: PostCardProps) {
    return (
        <article className="not-typeset flex flex-col gap-2">
            <h3>
                <Link
                    variant="underline"
                    to="/posts/$slug"
                    params={{ slug: post.slug }}
                    className="line-clamp-2 leading-6 whitespace-normal"
                >
                    {post.title}
                </Link>
            </h3>
            <p className="text-muted-foreground line-clamp-2 text-base leading-6">
                {post.subtitle}
            </p>
            <div className="space-x-2 truncate leading-6">
                {post.tags.map((tag) => (
                    <Link
                        key={tag.id}
                        variant="fade"
                        className="text-muted-foreground hover:text-foreground inline text-sm md:text-sm"
                        to="/tags/$slug"
                        params={{ slug: tag.slug }}
                    >
                        {tag.name}
                    </Link>
                ))}
            </div>
            <p className="text-muted-foreground text-sm leading-6">
                {format(post.updated_at, 'd MMM yyyy', { locale: id })}
            </p>
        </article>
    );
}
