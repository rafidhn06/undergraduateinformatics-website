import { useState } from 'react';

import { Link, useNavigate } from '@tanstack/react-router';

import { IconListSearch } from '@tabler/icons-react';
import { X } from 'lucide-react';

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

    return (
        <nav className="sticky top-0 z-50 h-18 w-full bg-white">
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
                                    navigate({ to: '/posts', search: { q: value } });
                                    setSearchValue('');
                                }}
                            />
                        </div>
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

                <Button
                    variant="ghost"
                    size="icon"
                    className="-mr-2 size-10 text-gray-600 lg:hidden"
                    onClick={onToggleSidebar}
                    aria-label="Toggle navigation menu"
                >
                    {isSidebarOpen ? (
                        <X className="size-7" strokeWidth={1.5} />
                    ) : (
                        <IconListSearch className="size-7" strokeWidth={1.5} />
                    )}
                </Button>
            </div>
        </nav>
    );
}
