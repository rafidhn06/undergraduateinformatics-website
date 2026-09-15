import type { Story, StoryDefault } from '@ladle/react';

import { ErrorState } from '@/components/ErrorState';
import { RouterHarness } from '@/components/RouterHarness';

import { TagDetailContent } from './TagDetailContent';
import { TagDetailSkeleton, TagNotFound } from './TagDetailStates';
import { type TagWithPosts } from './types';

const tagFixture: TagWithPosts = {
    id: 1,
    slug: 'beasiswa',
    name: 'Beasiswa',
    description: 'Informasi beasiswa dalam dan luar negeri untuk mahasiswa',
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
        {
            id: 3,
            slug: 'beasiswa-2024',
            title: 'Beasiswa 2024',
            subtitle: 'Periode lama',
            updated_at: '2024-01-10T12:00:00.000Z',
            tags: [{ id: 1, slug: 'beasiswa', name: 'Beasiswa' }],
        },
    ],
};

export default {
    title: 'Tags/Detail',
} satisfies StoryDefault;

export const Detail: Story = () => (
    <RouterHarness>
        <TagDetailContent tag={tagFixture} />
    </RouterHarness>
);
Detail.meta = { width: 'large' };

export const DetailMobile: Story = () => (
    <RouterHarness>
        <TagDetailContent tag={tagFixture} />
    </RouterHarness>
);
DetailMobile.meta = { width: 'medium' };

export const DetailEmpty: Story = () => (
    <RouterHarness>
        <TagDetailContent tag={{ ...tagFixture, posts: [] }} />
    </RouterHarness>
);
DetailEmpty.meta = { width: 'large' };

export const NotFound: Story = () => (
    <RouterHarness>
        <TagNotFound />
    </RouterHarness>
);

export const DetailLoading: Story = () => <TagDetailSkeleton />;

export const DetailError: Story = () => <ErrorState />;
