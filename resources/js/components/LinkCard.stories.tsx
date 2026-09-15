import type { Story, StoryDefault } from '@ladle/react';

import { RouterHarness } from '@/components/RouterHarness';

import { type LinkSummary } from '../types/link';
import { LinkCard } from './LinkCard';
import { LinkCardSkeleton } from './LinkCardStates';

const linkFixture: LinkSummary = {
    id: 7,
    name: 'Portal Akademik',
    link: 'https://portal.telkomuniversity.ac.id/',
    updated_at: '2026-09-05T12:00:00.000Z',
    section: { id: 2, name: 'Akademik' },
};

export default {
    title: 'LinkCard',
} satisfies StoryDefault;

export const Default: Story = () => (
    <RouterHarness>
        <div className="mx-auto w-full max-w-3xl p-4">
            <LinkCard link={linkFixture} />
        </div>
    </RouterHarness>
);

const longLinkFixture: LinkSummary = {
    id: 42,
    name: 'Portal Akademik dan Sistem Informasi Akademik Mahasiswa untuk Pengisian Rencana Studi serta Pendaftaran Ulang Semester Ganjil',
    link: 'https://portal.telkomuniversity.ac.id/',
    updated_at: '2026-09-05T12:00:00.000Z',
    section: { id: 1, name: 'Sistem Informasi Akademik dan Portal Mahasiswa' },
};

export const LongText: Story = () => (
    <RouterHarness>
        <div className="mx-auto w-full max-w-3xl p-4">
            <LinkCard link={longLinkFixture} />
        </div>
    </RouterHarness>
);

export const Loading: Story = () => <LinkCardSkeleton />;
