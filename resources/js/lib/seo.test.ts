import { describe, expect, it } from 'vitest';

import { seoDefaults, seoHead, seoPage } from './seo';

describe('seo', () => {
    it('reads the home page entry from the shared config', () => {
        expect(seoPage('home').title).toBe('Beranda - Portal Informasi Sarjana Informatika');
    });

    it('reads the tags page entry from the shared config', () => {
        expect(seoPage('tagList').title).toBe(
            'Daftar Topik - Portal Informasi Sarjana Informatika'
        );
        expect(seoPage('tagList').description).toBe(
            'Kumpulan topik informasi perkuliahan peserta didik Program Studi Sarjana Informatika Telkom University'
        );
    });

    it('builds head meta for a static page', () => {
        const head = seoHead('feedback');

        expect(head.meta[0]).toEqual({ title: 'Masukan - Portal Informasi Sarjana Informatika' });
        expect(head.meta[1]).toEqual({
            name: 'description',
            content:
                'Sampaikan pengaduan, keluhan, atau aspirasi terkait layanan akademik maupun non-akademik Program Studi Sarjana Informatika Telkom University',
        });
    });

    it('overrides title and description with dynamic values', () => {
        const head = seoHead('tagList', {
            title: 'Kurikulum - Portal Informasi Sarjana Informatika',
            description: 'Info kurikulum',
        });

        expect(head.meta[0]).toEqual({ title: 'Kurikulum - Portal Informasi Sarjana Informatika' });
        expect(head.meta[1]).toEqual({ name: 'description', content: 'Info kurikulum' });
    });

    it('exposes site defaults', () => {
        expect(seoDefaults.title).toBe('Portal Informasi Sarjana Informatika');
        expect(seoDefaults.description).toBe(
            'Sumber informasi resmi Program Studi Sarjana Informatika Telkom University untuk perkuliahan peserta didik.'
        );
    });
});
