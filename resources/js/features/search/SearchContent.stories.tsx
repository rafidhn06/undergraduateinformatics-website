import type { Story, StoryDefault } from '@ladle/react';

import { RouterHarness } from '@/components/RouterHarness';

import { SearchContent } from './SearchContent';
import { SearchSkeleton } from './SearchStates';
import { type SearchResult } from './types';

const resultFixture: SearchResult = {
    posts: [
        {
            id: 8,
            slug: 'beasiswa-luar-negeri',
            title: 'Beasiswa Luar Negeri',
            subtitle: 'Daftar sekarang',
            updated_at: '2026-09-01T12:00:00.000Z',
            tags: [{ id: 1, slug: 'beasiswa', name: 'Beasiswa' }],
        },
        {
            id: 7,
            slug: 'pendaftaran-beasiswa-2026',
            title: 'Pendaftaran Beasiswa 2026',
            subtitle: 'Periode baru dibuka',
            updated_at: '2026-04-05T12:00:00.000Z',
            tags: [{ id: 1, slug: 'beasiswa', name: 'Beasiswa' }],
        },
    ],
    meta: { current_page: 2, per_page: 10, total: 42, last_page: 5 },
};

export default {
    title: 'Search',
} satisfies StoryDefault;

export const Results: Story = () => (
    <RouterHarness>
        <SearchContent
            q="beasiswa"
            result={resultFixture}
            onSearch={() => undefined}
            onPageChange={() => undefined}
        />
    </RouterHarness>
);
Results.meta = { width: 'large' };

export const EmptyQuery: Story = () => (
    <RouterHarness>
        <SearchContent
            q=""
            result={resultFixture}
            onSearch={() => undefined}
            onPageChange={() => undefined}
        />
    </RouterHarness>
);
EmptyQuery.meta = { width: 'large' };

export const NoResults: Story = () => (
    <RouterHarness>
        <SearchContent
            q="xyz"
            result={{ posts: [], meta: { ...resultFixture.meta, total: 0 } }}
            onSearch={() => undefined}
            onPageChange={() => undefined}
        />
    </RouterHarness>
);
NoResults.meta = { width: 'large' };

export const Loading: Story = () => <SearchSkeleton />;
Loading.meta = { width: 'large' };
