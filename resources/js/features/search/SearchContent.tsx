import { ArticleContainer } from '@/components/ArticleContainer';
import { PostCard } from '@/components/PostCard';
import { SearchBox } from '@/components/SearchBar';
import { TextButton } from '@/components/TextButton';

import { type SearchResult } from './types';

interface SearchContentProps {
    q: string;
    result: SearchResult;
    onSearch: (value: string) => void;
    onPageChange: (page: number) => void;
}

export function SearchContent({ q, result, onSearch, onPageChange }: SearchContentProps) {
    const { posts, meta } = result;
    const hasQuery = q.trim().length > 0;

    return (
        <ArticleContainer>
            <SearchBox
                key={q ?? 'no-query'}
                defaultValue={q}
                className="w-full"
                onSubmit={onSearch}
            />
            {hasQuery ? (
                <p className="text-muted-foreground mt-[22.5px] md:mt-5">{meta.total} hasil</p>
            ) : (
                <p className="text-muted-foreground mt-[22.5px] md:mt-5">
                    Masukkan kata kunci untuk mencari.
                </p>
            )}

            {posts.length === 0 ? (
                <p role="status" className="text-muted-foreground mt-[22.5px] md:mt-5">
                    {hasQuery ? `Tidak ada hasil untuk “${q}”` : 'Belum ada informasi.'}
                </p>
            ) : (
                <>
                    <div className="mt-[22.5px] flex flex-col gap-4 md:mt-5">
                        {posts.map((post) => (
                            <PostCard key={post.id} post={post} />
                        ))}
                    </div>
                    <div className="mt-[22.5px] grid grid-cols-[1fr_auto_1fr] items-center md:mt-5">
                        <TextButton
                            variant="fade"
                            className="justify-self-start border-0"
                            disabled={meta.currentPage <= 1}
                            onClick={() => onPageChange(meta.currentPage - 1)}
                        >
                            Kembali
                        </TextButton>
                        <span className="text-muted-foreground text-lg leading-[31.5px] md:text-base md:leading-7">
                            {meta.currentPage} dari {meta.lastPage}
                        </span>
                        <TextButton
                            variant="fade"
                            className="justify-self-end border-0"
                            disabled={meta.currentPage >= meta.lastPage}
                            onClick={() => onPageChange(meta.currentPage + 1)}
                        >
                            Lanjut
                        </TextButton>
                    </div>
                </>
            )}
        </ArticleContainer>
    );
}
