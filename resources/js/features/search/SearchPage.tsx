import { useNavigate } from '@tanstack/react-router';

import { ErrorState } from '@/components/ErrorState';

import { SearchContent } from './SearchContent';
import { SearchSkeleton } from './SearchStates';
import { usePostSearch } from './usePostSearch';

export function SearchPage({ q, page, limit = 10 }: { q: string; page: number; limit?: number }) {
    const navigate = useNavigate();
    const query = usePostSearch(q, page, limit);

    if (query.isPending) {
        return <SearchSkeleton />;
    }

    if (query.isError) {
        return <ErrorState />;
    }

    return (
        <SearchContent
            q={q}
            result={query.data}
            onSearch={(value) => navigate({ to: '/posts', search: { q: value } })}
            onPageChange={(nextPage) =>
                navigate({
                    to: '/posts',
                    search: { q: q || undefined, page: nextPage },
                })
            }
        />
    );
}
