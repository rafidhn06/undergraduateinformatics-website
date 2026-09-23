import { useMemo } from 'react';

import { PostCard } from '@/components/PostCard';
import { TocLayout } from '@/components/TocLayout';
import { sectionId } from '@/lib/sectionId';

import { groupPostsByMonth } from './groupPostsByMonth';
import { type TagWithPosts } from './types';

export function TagDetailContent({ tag }: { tag: TagWithPosts }) {
    const groups = useMemo(() => groupPostsByMonth(tag.posts), [tag.posts]);

    const tocItems = groups.map((group) => ({
        id: sectionId('tag-section', group.key),
        label: group.label,
    }));

    return (
        <TocLayout
            title={tag.name}
            description={tag.description ?? undefined}
            items={tocItems}
            emptyMessage="Belum ada postingan untuk topik ini."
        >
            {groups.map((group) => (
                <section key={group.key}>
                    <h2
                        id={sectionId('tag-section', group.key)}
                        className="scroll-mt-28 md:scroll-mt-27"
                    >
                        {group.label}
                    </h2>
                    <div className="mt-[22.5px] flex flex-col gap-4 md:mt-5">
                        {group.posts.map((post) => (
                            <PostCard key={post.id} post={post} />
                        ))}
                    </div>
                </section>
            ))}
        </TocLayout>
    );
}
