import type { Story, StoryDefault } from '@ladle/react';

import { RouterHarness } from '@/components/RouterHarness';

import { type PostSummary } from '../types/post';
import { PostCard } from './PostCard';
import { PostCardSkeleton } from './PostCardStates';

const postFixture: PostSummary = {
    id: 7,
    slug: 'pengumuman-beasiswa-2026',
    title: 'Pengumuman Beasiswa 2026',
    subtitle: 'Pendaftaran beasiswa dibuka hingga akhir bulan.',
    updated_at: '2026-09-05T12:00:00.000Z',
    tags: [
        { id: 1, slug: 'beasiswa', name: 'Beasiswa' },
        { id: 2, slug: 'akademik', name: 'Akademik' },
    ],
};

export default {
    title: 'PostCard',
} satisfies StoryDefault;

export const Default: Story = () => (
    <RouterHarness>
        <div className="mx-auto w-full max-w-3xl p-4">
            <PostCard post={postFixture} />
        </div>
    </RouterHarness>
);

const longPostFixture: PostSummary = {
    id: 42,
    slug: 'pengumuman-perpanjangan-masa-pendaftaran-ulang',
    title: 'Pengumuman Perpanjangan Masa Pendaftaran Ulang Mahasiswa Baru Jalur Prestasi dan Jalur Mandiri Tahun Akademik 2026/2027',
    subtitle:
        'Mengingat tingginya antusiasme calon mahasiswa, pendaftaran ulang diperpanjang hingga tanggal 30 September 2026 dengan seluruh persyaratan administrasi tetap berlaku, termasuk pengunggahan dokumen legalisir dan pembayaran UKT tahap pertama.',
    updated_at: '2026-09-05T12:00:00.000Z',
    tags: [
        { id: 1, slug: 'beasiswa', name: 'Beasiswa' },
        { id: 2, slug: 'akademik', name: 'Akademik' },
        { id: 3, slug: 'mbkm', name: 'MBKM' },
        { id: 4, slug: 'pengumuman', name: 'Pengumuman' },
        { id: 5, slug: 'pmb', name: 'PMB' },
        { id: 6, slug: 'ukt', name: 'UKT' },
        { id: 7, slug: 'herregistrasi', name: 'Herregistrasi' },
        { id: 8, slug: 's1-informatika', name: 'S1 Informatika' },
    ],
};

export const LongTextManyTags: Story = () => (
    <RouterHarness>
        <div className="mx-auto w-full max-w-3xl p-4">
            <PostCard post={longPostFixture} />
        </div>
    </RouterHarness>
);

export const Loading: Story = () => <PostCardSkeleton />;
