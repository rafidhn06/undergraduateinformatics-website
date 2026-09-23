import { useNavigate } from '@tanstack/react-router';

import { usePostList } from '../post/page-data';
import { SearchContent } from './SearchContent';

export function SearchPage({
    q,
    page,
    perPage = 10,
}: {
    q: string;
    page: number;
    perPage?: number;
}) {
    const navigate = useNavigate();
    const { data } = usePostList(q, page, perPage);

    return (
        <SearchContent
            q={q}
            result={data}
            onSearch={(value) => navigate({ to: '/posts', search: { q: value, page: 1, perPage } })}
            onPageChange={(nextPage) =>
                navigate({
                    to: '/posts',
                    search: { q: q || undefined, page: nextPage, perPage },
                })
            }
        />
    );
}
