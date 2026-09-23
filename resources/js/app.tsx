import React from 'react';
import ReactDOM from 'react-dom/client';

import { QueryClient, QueryClientProvider } from '@tanstack/react-query';
import { RouterProvider, createRouter } from '@tanstack/react-router';

import NProgress from 'nprogress';

import '../css/app.css';
import './bootstrap';
import { ErrorPage } from './components/ErrorPage';
import { NotFoundPage } from './components/NotFoundPage';
import { seedInitialQueries } from './hooks/usePageData';
import { routeTree } from './routeTree.gen';

const queryClient = new QueryClient({
    defaultOptions: {
        queries: {
            refetchOnWindowFocus: false,
            retry: 1,
            staleTime: 30000,
        },
    },
});

seedInitialQueries(queryClient);

const router = createRouter({
    routeTree,
    context: { queryClient },
    defaultPreload: 'intent',
    defaultPreloadStaleTime: 0,
    notFoundMode: 'root',
    defaultNotFoundComponent: NotFoundPage,
    defaultErrorComponent: ErrorPage,
    scrollRestoration: true,
});

NProgress.configure({ showSpinner: false });

router.subscribe('onBeforeLoad', () => {
    NProgress.start();
});

router.subscribe('onLoad', () => {
    NProgress.done();
});

declare module '@tanstack/react-router' {
    interface Register {
        router: typeof router;
    }
}

function removeServerRenderedHeadTags() {
    document.head
        .querySelectorAll('[data-ssr="true"]:not([property^="og:"])')
        .forEach((el) => el.remove());
}

const rootElement = document.getElementById('root');

if (rootElement) {
    removeServerRenderedHeadTags();

    ReactDOM.createRoot(rootElement).render(
        <React.StrictMode>
            <QueryClientProvider client={queryClient}>
                <RouterProvider router={router} />
            </QueryClientProvider>
        </React.StrictMode>
    );
}
