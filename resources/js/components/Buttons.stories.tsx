import type { Story } from '@ladle/react';

import { Link } from './Link';
import { PrimaryButton } from './PrimaryButton';
import { RouterHarness } from './RouterHarness';
import { SearchBar } from './SearchBar';
import { SecondaryButton } from './SecondaryButton';
import { TextButton } from './TextButton';

export default {
    title: 'Primitives/Buttons',
};

export const Primary: Story = () => <PrimaryButton>Kirim</PrimaryButton>;

export const Secondary: Story = () => <SecondaryButton>Kembali</SecondaryButton>;

export const Search: Story = () => <SearchBar />;

export const TextButtonFade: Story = () => <TextButton variant="fade">Kembali</TextButton>;

export const TextButtonUnderline: Story = () => (
    <TextButton variant="underline">Isi Formulir Lagi</TextButton>
);

export const LinkFade: Story = () => (
    <RouterHarness>
        <Link variant="fade" to="/">
            Kembali
        </Link>
    </RouterHarness>
);

export const LinkUnderline: Story = () => (
    <RouterHarness>
        <Link variant="underline" to="/">
            Isi Formulir Lagi
        </Link>
    </RouterHarness>
);
