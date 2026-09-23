import { useCallback, useEffect, useState } from 'react';

const STORAGE_KEY = 'public-theme';

function resolveInitialTheme(): boolean {
    const storedTheme = window.localStorage.getItem(STORAGE_KEY);

    if (storedTheme === 'dark' || storedTheme === 'light') {
        return storedTheme === 'dark';
    }

    return window.matchMedia('(prefers-color-scheme: dark)').matches;
}

export function usePublicTheme() {
    const [isDark, setIsDark] = useState(resolveInitialTheme);

    useEffect(() => {
        document.documentElement.classList.toggle('dark', isDark);
        window.localStorage.setItem(STORAGE_KEY, isDark ? 'dark' : 'light');
    }, [isDark]);

    const toggleTheme = useCallback(() => {
        setIsDark((previous) => !previous);
    }, []);

    useEffect(() => {
        const handleStorage = (event: StorageEvent) => {
            if (event.key !== STORAGE_KEY) {
                return;
            }

            if (event.newValue === 'dark' || event.newValue === 'light') {
                setIsDark(event.newValue === 'dark');
            }
        };

        window.addEventListener('storage', handleStorage);

        return () => {
            window.removeEventListener('storage', handleStorage);
        };
    }, []);

    return { isDark, toggleTheme };
}
