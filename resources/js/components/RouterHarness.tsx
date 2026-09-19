import { type ReactNode, useMemo } from 'react';

import { createMemoryHistory } from '@tanstack/history';
import { RouterProvider, createRootRoute, createRoute, createRouter } from '@tanstack/react-router';

interface RouterHarnessProps {
    children: ReactNode;
}

export function RouterHarness({ children }: RouterHarnessProps) {
    const router = useMemo(() => {
        const rootRoute = createRootRoute({
            component: () => <>{children}</>,
        });
        const routeTree = rootRoute.addChildren([
            createRoute({ getParentRoute: () => rootRoute, path: '/' }),
            createRoute({ getParentRoute: () => rootRoute, path: 'posts/$slug' }),
            createRoute({ getParentRoute: () => rootRoute, path: 'posts' }),
            createRoute({ getParentRoute: () => rootRoute, path: 'tags' }),
            createRoute({ getParentRoute: () => rootRoute, path: 'tags/$slug' }),
            createRoute({ getParentRoute: () => rootRoute, path: 'explore' }),
            createRoute({ getParentRoute: () => rootRoute, path: 'link' }),
            createRoute({ getParentRoute: () => rootRoute, path: 'links' }),
            createRoute({ getParentRoute: () => rootRoute, path: 'feedback' }),
            createRoute({ getParentRoute: () => rootRoute, path: 'reservation' }),
            createRoute({ getParentRoute: () => rootRoute, path: 'admin' }),
        ]);

        return createRouter({
            routeTree,
            history: createMemoryHistory({ initialEntries: ['/explore'] }),
        });
    }, [children]);

    return <RouterProvider router={router} />;
}
