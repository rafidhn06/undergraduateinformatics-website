import { type ComponentProps, useState } from 'react';

import { Search } from 'lucide-react';

import { cn } from '../lib/utils';
import { Input } from './ui/input';

interface SearchBarProps extends Omit<ComponentProps<typeof Input>, 'onSubmit'> {
    onSubmit?: (value: string) => void;
}

function SearchBarBase({
    className,
    onSubmit,
    onKeyDown,
    onBlur,
    filled,
    ...props
}: SearchBarProps & { filled: boolean }) {
    const [isPointerSession, setIsPointerSession] = useState(false);

    return (
        <div
            onPointerDown={() => setIsPointerSession(true)}
            className={cn(
                'has-focus-visible:global-ring relative flex w-48 items-center',
                isPointerSession && 'no-ring',
                filled ? 'bg-muted px-3 py-1.5' : 'border-b px-0 py-1.5',
                className
            )}
        >
            <Input
                type="text"
                placeholder="Cari..."
                {...props}
                onBlur={(event) => {
                    setIsPointerSession(false);
                    onBlur?.(event);
                }}
                onKeyDown={(event) => {
                    onKeyDown?.(event);

                    if (event.key === 'Enter') {
                        event.preventDefault();
                        onSubmit?.(event.currentTarget.value);
                    }
                }}
                className="no-ring h-auto w-full border-none bg-transparent p-0 pr-8 text-lg md:text-base dark:bg-transparent"
            />
            <Search className="text-muted-foreground absolute top-1/2 right-3 size-4 -translate-y-1/2" />
        </div>
    );
}

export function SearchBar(props: SearchBarProps) {
    return <SearchBarBase {...props} filled />;
}

export function SearchBox(props: SearchBarProps) {
    return <SearchBarBase {...props} filled={false} />;
}
