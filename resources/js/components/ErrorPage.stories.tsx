import type { Story } from '@ladle/react';

import { ErrorPage } from './ErrorPage';
import { ErrorState } from './ErrorState';
import { NotFoundPage } from './NotFoundPage';
import { RouterHarness } from './RouterHarness';

export default {
    title: 'Status Pages/ErrorPage',
};

export const Default: Story = () => <ErrorPage />;

export const WithError: Story = () => <ErrorPage error={{ message: 'Contoh pesan kesalahan' }} />;

export const ErrorStateDefault: Story = () => <ErrorState />;

export const NotFoundPageDefault: Story = () => (
    <RouterHarness>
        <NotFoundPage />
    </RouterHarness>
);
