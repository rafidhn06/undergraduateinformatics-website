import { useState } from 'react';

import { useNavigate } from '@tanstack/react-router';

import { cn } from '../lib/utils';
import { NavItem } from './NavItem';
import { SearchBar } from './SearchBar';
import { buttonVariants } from './ui/button';

interface SideBarProps {
    isOpen: boolean;
    onClose: () => void;
}

export function SideBar({ isOpen, onClose }: SideBarProps) {
    const navigate = useNavigate();
    const [searchValue, setSearchValue] = useState('');

    return (
        <aside
            className={`bg-background fixed top-18 bottom-0 left-0 z-50 mr-16 w-full max-w-xs scrollbar-none overflow-y-auto transition-transform duration-300 ease-in-out lg:hidden ${
                isOpen ? 'translate-x-0' : '-translate-x-full'
            }`}
        >
            <div className="flex flex-col gap-4">
                <div className="mx-6 mt-6">
                    <SearchBar
                        value={searchValue}
                        onChange={(event) => setSearchValue(event.target.value)}
                        className="w-full px-4 py-2"
                        onSubmit={(value) => {
                            navigate({ to: '/posts', search: { q: value } });
                            setSearchValue('');
                            onClose();
                        }}
                    />
                </div>

                <div className="flex flex-col">
                    <div className="mx-3 my-0.5">
                        <NavItem variant="side" to="/" onClick={onClose}>
                            Beranda
                        </NavItem>
                    </div>
                    <div className="mx-3 my-0.5">
                        <NavItem variant="side" to="/tags" onClick={onClose}>
                            Informasi
                        </NavItem>
                    </div>
                    <div className="mx-3 my-0.5">
                        <NavItem variant="side" to="/links" onClick={onClose}>
                            Tautan
                        </NavItem>
                    </div>
                    <div className="mx-3 my-0.5">
                        <NavItem variant="side" to="/feedback" onClick={onClose}>
                            Masukan
                        </NavItem>
                    </div>
                    <div className="mx-3 my-0.5">
                        <NavItem variant="side" to="/reservation" onClick={onClose}>
                            Pertemuan
                        </NavItem>
                    </div>
                    <hr className="border-border mx-6 my-2 border-t" />
                    <div className="mx-3 my-0.5">
                        <a
                            href="/admin/login"
                            onClick={onClose}
                            className={cn(
                                buttonVariants({ variant: 'ghost' }),
                                'text-muted-foreground active:bg-muted h-auto w-full justify-start py-3.5 pr-0 pl-3 text-lg md:text-base'
                            )}
                        >
                            Masuk
                        </a>
                    </div>
                </div>
            </div>
        </aside>
    );
}
