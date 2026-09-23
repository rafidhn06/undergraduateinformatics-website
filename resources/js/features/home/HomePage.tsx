import { useSuspensePageData } from '@/hooks/usePageData';
import { type LinkSummary } from '@/types/link';

import { usePostList } from '../post/page-data';
import { HomeContent } from './HomeContent';
import { type DashboardDataset, type DatasetsPayload, type ImportantLinksPayload } from './types';

export function HomePage() {
    const { data: postList } = usePostList('', 1, 5);
    const posts = postList.posts;

    const { data: links } = useSuspensePageData<ImportantLinksPayload, LinkSummary[]>(
        '/api/important-links',
        {
            select: (response) => response.data,
        },
        { per_page: 5 }
    );

    const { data: datasets } = useSuspensePageData<DatasetsPayload, DashboardDataset[]>(
        '/api/datasets',
        {
            select: (response) => response.data,
        }
    );

    return <HomeContent data={{ latestPosts: posts, latestLinks: links, dashboard: datasets }} />;
}
