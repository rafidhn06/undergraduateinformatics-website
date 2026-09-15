import type { Story, StoryDefault } from '@ladle/react';

import { ErrorState } from '@/components/ErrorState';
import { RouterHarness } from '@/components/RouterHarness';

import { TagListContent } from './TagListContent';
import { TagListSkeleton } from './TagListStates';
import { type TagWithCount } from './types';

const tagsFixture: TagWithCount[] = [
    {
        id: 1,
        slug: 's1-informatika',
        name: 'S1 Informatika',
        description: 'Informasi resmi Program Studi Sarjana Informatika Telkom University',
        posts_count: 4,
    },
    {
        id: 2,
        slug: 'beasiswa',
        name: 'Beasiswa',
        description: 'Informasi beasiswa dalam dan luar negeri untuk mahasiswa',
        posts_count: 2,
    },
    {
        id: 3,
        slug: 'akademik',
        name: 'Akademik',
        description: 'Pengumuman akademik dan jadwal perkuliahan',
        posts_count: 3,
    },
    { id: 4, slug: 'mbkm', name: 'MBKM', description: null, posts_count: 0 },
];

export default {
    title: 'Tags',
} satisfies StoryDefault;

export const List: Story = () => (
    <RouterHarness>
        <TagListContent tags={tagsFixture} />
    </RouterHarness>
);

export const Empty: Story = () => (
    <RouterHarness>
        <TagListContent tags={[]} />
    </RouterHarness>
);

export const Loading: Story = () => <TagListSkeleton />;

export const Error: Story = () => <ErrorState />;
