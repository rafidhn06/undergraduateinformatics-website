import { type PostSearchPayload } from '@/features/search/types';
import { useSuspensePageData } from '@/hooks/usePageData';
import { type LinkSummary } from '@/types/link';
import { type PostSummary } from '@/types/post';

import { HomeContent } from './HomeContent';
import { type DashboardDataset, type DatasetsPayload, type ImportantLinksPayload } from './types';

export function HomePage() {
    const { data: posts } = useSuspensePageData<PostSearchPayload, PostSummary[]>(
        '/api/posts',
        {
            select: (response) => response.data,
        },
        { per_page: 5 }
    );

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

    return <HomeContent data={{ latest_posts: posts, latest_links: links, dashboard: datasets }} />;
}
