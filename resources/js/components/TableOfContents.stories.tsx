import type { Story, StoryDefault } from '@ladle/react';

import { TableOfContents } from './TableOfContents';
import { TableOfContentsSkeleton } from './TableOfContentsStates';

const itemsFixture = [
    { id: 'link-section-1', label: 'Akademik' },
    { id: 'link-section-2', label: 'MBKM' },
    { id: 'link-section-6', label: 'Alumni' },
];

export default {
    title: 'TableOfContents',
} satisfies StoryDefault;

export const Default: Story = () => (
    <TableOfContents items={itemsFixture} onSelect={() => undefined} />
);

export const Loading: Story = () => <TableOfContentsSkeleton />;
