import type { Story, StoryDefault } from '@ladle/react';

import { ErrorState } from '@/components/ErrorState';
import { RouterHarness } from '@/components/RouterHarness';
import { type Post } from '@/types/post';

import { PostContent } from './PostContent';
import { PostNotFound, PostSkeleton } from './PostStates';

const postFixture: Post = {
    id: 1,
    slug: 'registrasi-ganjil',
    title: 'Informasi Registrasi Ganjil 2026',
    subtitle: 'Periode registrasi dan tata cara pembayaran',
    body: '<p>Berikut informasi resmi registrasi Ganjil 2026.</p><p>Mohon membaca seluruh ketentuan dengan teliti.</p>',
    image: 'images/placeholder.png',
    created_at: '2026-09-01T00:00:00.000Z',
    updated_at: '2026-09-05T00:00:00.000Z',
    tags: [{ id: 1, slug: 'registrasi', name: 'Registrasi' }],
};

export default {
    title: 'Posts',
} satisfies StoryDefault;

export const Detail: Story = () => (
    <RouterHarness>
        <PostContent post={postFixture} />
    </RouterHarness>
);
Detail.meta = { width: 'large' };

export const DetailMobile: Story = () => (
    <RouterHarness>
        <PostContent post={postFixture} />
    </RouterHarness>
);
DetailMobile.meta = { width: 'medium' };

export const DetailWithoutImage: Story = () => (
    <RouterHarness>
        <PostContent post={{ ...postFixture, image: null }} />
    </RouterHarness>
);
DetailWithoutImage.meta = { width: 'large' };

const richTextBody = `
  <h2>Latar Belakang</h2>
  <p>Paragraf pengantar dengan <strong>teks tebal</strong> dan <em>teks miring</em>.</p>
  <h3>Poin Penting</h3>
  <ul>
    <li>Butir daftar pertama</li>
    <li>Butir daftar kedua</li>
  </ul>
  <ol>
    <li>Langkah pertama</li>
    <li>Langkah kedua</li>
  </ol>
  <p>Lihat <a href="/">panduan resmi</a> untuk detail.</p>
  <table>
    <thead><tr><th>Kegiatan</th><th>Waktu</th></tr></thead>
    <tbody><tr><td>Registrasi</td><td>1-10 Sep 2026</td></tr></tbody>
  </table>
`;

export const DetailRichText: Story = () => (
    <RouterHarness>
        <PostContent post={{ ...postFixture, body: richTextBody }} />
    </RouterHarness>
);
DetailRichText.meta = { width: 'large' };

export const NotFound: Story = () => (
    <RouterHarness>
        <PostNotFound />
    </RouterHarness>
);

export const DetailLoading: Story = () => <PostSkeleton />;
DetailLoading.meta = { width: 'large' };

export const DetailError: Story = () => <ErrorState />;
DetailError.meta = { width: 'large' };
