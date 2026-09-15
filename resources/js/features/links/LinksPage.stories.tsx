import type { Story, StoryDefault } from '@ladle/react';

import { ErrorState } from '@/components/ErrorState';
import { RouterHarness } from '@/components/RouterHarness';

import { LinksContent } from './LinksContent';
import { LinksSkeleton } from './LinksStates';
import { type LinkSection } from './types';

const sectionsFixture: LinkSection[] = [
    {
        id: 1,
        name: 'Akademik',
        order_number: 1,
        links: [
            {
                id: 3,
                name: 'SIAKAD',
                link: 'https://siakad.telkomuniversity.ac.id',
                updated_at: '2026-09-04T10:00:00.000000Z',
            },
            {
                id: 4,
                name: 'E-Campus',
                link: 'https://ecampus.telkomuniversity.ac.id',
                updated_at: '2026-09-04T09:00:00.000000Z',
            },
        ],
    },
    {
        id: 2,
        name: 'MBKM',
        order_number: 2,
        links: [
            {
                id: 5,
                name: 'Portal MBKM',
                link: 'https://mbkm.kemdikbud.go.id',
                updated_at: '2026-09-03T10:00:00.000000Z',
            },
        ],
    },
    {
        id: 6,
        name: 'Alumni',
        order_number: 3,
        links: [],
    },
];

export default {
    title: 'Links',
} satisfies StoryDefault;

export const Desktop: Story = () => (
    <RouterHarness>
        <LinksContent sections={sectionsFixture} />
    </RouterHarness>
);
Desktop.meta = { width: 'large' };

export const MobileTablet: Story = () => (
    <RouterHarness>
        <LinksContent sections={sectionsFixture} />
    </RouterHarness>
);
MobileTablet.meta = { width: 'medium' };

export const Loading: Story = () => <LinksSkeleton />;
Loading.meta = { width: 'large' };

export const LoadingMobileTablet: Story = () => <LinksSkeleton />;
LoadingMobileTablet.meta = { width: 'medium' };

export const Empty: Story = () => <LinksContent sections={[]} />;
Empty.meta = { width: 'large' };

export const Error: Story = () => <ErrorState />;
Error.meta = { width: 'large' };
