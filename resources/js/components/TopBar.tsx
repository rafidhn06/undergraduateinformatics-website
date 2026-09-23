import { useState } from 'react';

import { Link, useNavigate } from '@tanstack/react-router';

import { IconListSearch } from '@tabler/icons-react';
import { Moon, Sun, X } from 'lucide-react';

import { usePublicTheme } from '../hooks/usePublicTheme';
import { cn } from '../lib/utils';
import { NavItem } from './NavItem';
import { SearchBar } from './SearchBar';
import { Button, buttonVariants } from './ui/button';

interface TopBarProps {
    isSidebarOpen: boolean;
    onToggleSidebar: () => void;
}

export function TopBar({ isSidebarOpen, onToggleSidebar }: TopBarProps) {
    const navigate = useNavigate();
    const [searchValue, setSearchValue] = useState('');
    const { isDark, toggleTheme } = usePublicTheme();

    return (
        <nav className="bg-background sticky top-0 z-50 h-18 w-full">
            <div className="mx-auto flex h-full w-full max-w-7xl items-center justify-between px-4 py-3">
                <Link to="/">
                    <img
                        src="/images/logo.png"
                        alt="Logo"
                        className="h-full max-h-12 object-contain"
                    />
                </Link>

                <div className="hidden items-center lg:flex">
                    <div className="flex items-center">
                        <div className="ml-8 flex items-center gap-8">
                            <NavItem variant="top" to="/">
                                Beranda
                            </NavItem>
                            <NavItem variant="top" to="/tags">
                                Informasi
                            </NavItem>
                            <NavItem variant="top" to="/links">
                                Tautan
                            </NavItem>
                            <NavItem variant="top" to="/feedback">
                                Masukan
                            </NavItem>
                            <NavItem variant="top" to="/reservation">
                                Pertemuan
                            </NavItem>
                            <SearchBar
                                value={searchValue}
                                onChange={(event) => setSearchValue(event.target.value)}
                                onSubmit={(value) => {
                                    navigate({
                                        to: '/posts',
                                        search: { q: value, page: 1, perPage: 10 },
                                    });
                                    setSearchValue('');
                                }}
                            />
                        </div>
                        <Button
                            variant="ghost"
                            size="icon"
                            className="text-muted-foreground ml-3 size-10"
                            onClick={toggleTheme}
                            aria-label="Alihkan mode gelap"
                        >
                            {isDark ? (
                                <Sun className="size-5" strokeWidth={1.5} />
                            ) : (
                                <Moon className="size-5" strokeWidth={1.5} />
                            )}
                        </Button>
                        <a
                            href="/admin/login"
                            className={cn(
                                buttonVariants({ variant: 'default' }),
                                'ml-3 h-auto px-3 py-1.5 text-base'
                            )}
                        >
                            Masuk
                        </a>
                    </div>
                </div>

                <div className="flex items-center gap-1 lg:hidden">
                    <Button
                        variant="ghost"
                        size="icon"
                        className="text-muted-foreground size-10"
                        onClick={toggleTheme}
                        aria-label="Alihkan mode gelap"
                    >
                        {isDark ? (
                            <Sun className="size-7" strokeWidth={1.5} />
                        ) : (
                            <Moon className="size-7" strokeWidth={1.5} />
                        )}
                    </Button>
                    <Button
                        variant="ghost"
                        size="icon"
                        className="text-muted-foreground -mr-2 size-10"
                        onClick={onToggleSidebar}
                        aria-label="Alihkan menu navigasi"
                    >
                        {isSidebarOpen ? (
                            <X className="size-7" strokeWidth={1.5} />
                        ) : (
                            <IconListSearch className="size-7" strokeWidth={1.5} />
                        )}
                    </Button>
                </div>
            </div>
        </nav>
    );
}
