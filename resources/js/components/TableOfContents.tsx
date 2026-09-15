import { TextButton } from '@/components/TextButton';

export interface TocItem {
    id: string;
    label: string;
}

interface TableOfContentsProps {
    items: TocItem[];
    onSelect: (id: string) => void;
}

export function TableOfContents({ items, onSelect }: TableOfContentsProps) {
    return (
        <div className="typeset typeset-article lg:sticky lg:top-27">
            <h3 className="lg:bg-background lg:sticky lg:top-0 lg:z-10">Daftar Isi</h3>
            {items.length === 0 ? (
                <p className="text-muted-foreground">Belum ada bagian.</p>
            ) : (
                <nav aria-label="Daftar Isi">
                    <div className="border-border max-h-[12.25rem] overflow-y-auto border-l [direction:rtl] md:max-h-[10.75rem] lg:max-h-[calc(100vh-13rem)] lg:scrollbar-none [&>ul]:mt-0">
                        <ul className="pl-3 [direction:ltr] [&>li]:mt-0 [&>li+li]:mt-[0.5em]">
                            {items.map((item) => (
                                <li key={item.id} className="list-none">
                                    <TextButton
                                        variant="fade"
                                        className="text-muted-foreground hover:text-foreground text-left text-base whitespace-normal"
                                        onClick={() => onSelect(item.id)}
                                    >
                                        {item.label}
                                    </TextButton>
                                </li>
                            ))}
                        </ul>
                    </div>
                </nav>
            )}
        </div>
    );
}
