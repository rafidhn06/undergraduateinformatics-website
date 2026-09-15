import type { Story, StoryDefault } from '@ladle/react';

import { RichText } from './RichText';
import { RichTextContent } from './RichTextContent';

export default {
    title: 'Rich Text',
} satisfies StoryDefault;

export const Plain: Story = () => <RichText as="span" html="Teks polos tanpa format" />;

export const Formatted: Story = () => (
    <RichText
        as="span"
        html='Halo <b>tebal</b>, <i>miring</i>, <u>garis</u> dan <a href="https://example.com">link</a>'
    />
);

export const List: Story = () => (
    <RichText as="div" html="<ul><li>item satu</li><li>item dua</li></ul>" />
);

export const ContentPlain: Story = () => (
    <RichTextContent content={{ text: 'Teks polos' }} as="span" />
);

export const ContentRich: Story = () => (
    <RichTextContent content={{ text: 'Teks polos', html: 'Teks <b>kaya</b>' }} as="span" />
);

export const ContentEmpty: Story = () => <RichTextContent content={null} as="span" />;
