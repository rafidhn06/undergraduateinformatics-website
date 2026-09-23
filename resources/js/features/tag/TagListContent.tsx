import { ArticleContainer } from '@/components/ArticleContainer';
import { Link } from '@/components/Link';

import { type TagWithCount } from './types';

export function TagListContent({ tags }: { tags: TagWithCount[] }) {
    return (
        <ArticleContainer>
            <h1>Daftar Topik</h1>
            <p className="text-muted-foreground">
                Kumpulan topik informasi perkuliahan peserta didik Program Studi Sarjana Informatika
                Telkom University.
            </p>
            {tags.length === 0 ? (
                <p role="status" className="text-muted-foreground mt-8 md:mt-7">
                    Belum ada topik.
                </p>
            ) : (
                <ul>
                    {tags.map((tag) => (
                        <li key={tag.id}>
                            <Link
                                variant="underline"
                                className="whitespace-normal no-underline"
                                to="/tags/$slug"
                                params={{ slug: tag.slug }}
                            >
                                {tag.name} ({tag.postsCount})
                            </Link>
                            {tag.description && (
                                <p className="text-muted-foreground">{tag.description}</p>
                            )}
                        </li>
                    ))}
                </ul>
            )}
        </ArticleContainer>
    );
}
