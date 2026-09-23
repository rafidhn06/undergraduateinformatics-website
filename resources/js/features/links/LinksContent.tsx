import { useMemo } from 'react';

import { Link } from '@/components/Link';
import { TocLayout } from '@/components/TocLayout';
import { sectionId } from '@/lib/sectionId';

import { type LinkSection } from './types';

export function LinksContent({ sections }: { sections: LinkSection[] }) {
    const tocItems = useMemo(
        () =>
            sections.map((section) => ({
                id: sectionId('link-section', section.id),
                label: section.name,
            })),
        [sections]
    );

    return (
        <TocLayout
            title="Tautan Penting"
            description="Jelajahi tautan penting pendukung perkuliahan peserta didik Program Studi Sarjana Informatika Telkom University."
            items={tocItems}
            emptyMessage="Belum ada tautan penting."
        >
            {sections.map((section) => (
                <section key={section.id}>
                    <h2
                        id={sectionId('link-section', section.id)}
                        className="scroll-mt-28 md:scroll-mt-27"
                    >
                        {section.name}
                    </h2>
                    {section.links.length === 0 ? (
                        <p className="text-muted-foreground">Belum ada tautan pada section ini.</p>
                    ) : (
                        <ul>
                            {section.links.map((link) => (
                                <li key={link.id}>
                                    <Link
                                        variant="underline"
                                        className="whitespace-normal no-underline"
                                        to={link.link}
                                        target="_blank"
                                        rel="noopener noreferrer"
                                    >
                                        {link.name}
                                    </Link>
                                </li>
                            ))}
                        </ul>
                    )}
                </section>
            ))}
        </TocLayout>
    );
}
